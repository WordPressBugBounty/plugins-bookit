<?php

namespace Bookit\Classes\Database;

use Bookit\Classes\Vendor\DatabaseModel;

class Payments extends DatabaseModel {

	public static $defaultType    = 'locally';
	public static $freeType       = 'free';
	public static $completeType   = 'complete';
	public static $defaultStatus  = 'pending';
	public static $completeStatus = 'complete';
	public static $rejectedStatus = 'rejected';
	public static $statusList     = array( 'pending', 'cancelled', 'rejected', 'complete' );
	public static $typeList       = array( 'locally', 'stripeConnect', 'paypal', 'stripe', 'woocommerce', 'free' );

	/**
	 * How many times a contended claim is retried before giving up.
	 *
	 * @since 2.6.0.4
	 *
	 * @var int
	 */
	const CLAIM_MAX_ATTEMPTS = 3;

	/**
	 * Create Table
	 */
	public static function create_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table_name  = self::_table();
		$primary_key = self::$primary_key;

		$sql = "CREATE TABLE IF NOT EXISTS  {$table_name} (
			`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`appointment_id` INT UNSIGNED NOT NULL,
			`coupon_id` INT UNSIGNED DEFAULT NULL,
			`discount_id` INT UNSIGNED DEFAULT NULL,
			`type` ENUM('locally', 'stripeConnect', 'paypal', 'stripe', 'woocommerce', 'free') NOT NULL DEFAULT 'locally',
			`status` ENUM('pending', 'cancelled', 'rejected', 'complete') NOT NULL DEFAULT 'pending',
			`total`     DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `tax`       DECIMAL(10,2) DEFAULT 0.00,
            `transaction`     VARCHAR(255) DEFAULT NULL,
            `process_id`      VARCHAR(64) DEFAULT NULL,
			`notes` longtext DEFAULT NULL,
			`created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            `paid_at` DATETIME,
    		PRIMARY KEY ({$primary_key}),
            INDEX `idx_appointment_id` (`appointment_id`),
            INDEX `idx_coupon_id` (`coupon_id`),
            INDEX `idx_discount_id` (`discount_id`),
            INDEX `idx_status` (`status`),
            INDEX `idx_transaction` (`transaction`(191))
		) {$wpdb->get_charset_collate()};";

		maybe_create_table( $table_name, $sql );
	}

	/**
	 * Change Payment Status
	 * @param $id
	 * @param $payment_status
	 */
	public static function change_payment_status( $id, $payment_status ) {
		$data  = array( 'status' => $payment_status );
		$where = array( 'id' => $id );

		if ( 'complete' == $payment_status ) {
			$data['paid_at'] = wp_date( 'Y-m-d H:i:s' );
		}
		self::update( $data, $where );
	}

	/**
	 * Update Payment Methods Enum to Include stripeConnect
	 *
	 * @since 2.5.0
	 */
	public static function update_payment_methods_enum() {
		global $wpdb;

		$sql = sprintf(
			"ALTER TABLE `%s` MODIFY COLUMN `type` ENUM('locally', 'stripeConnect', 'paypal', 'stripe', 'woocommerce', 'free') NOT NULL DEFAULT 'locally';",
			esc_sql( self::_table() )
		);
		$wpdb->query( $sql );
	}

	/**
	 * Add the claim column and the `transaction` lookup index, and drop the
	 * unique index an earlier build may have left behind.
	 *
	 * The column is nullable and the index is not unique, so neither can fail
	 * on pre-existing data and every install ends up with the same schema.
	 *
	 * @since 2.6.0.4
	 *
	 * @return bool Whether the schema is in place.
	 */
	public static function add_payment_transaction_claim_schema() {
		global $wpdb;

		$table   = esc_sql( self::_table() );
		$clauses = array();

		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$table}` LIKE 'process_id'" ) ) {
			$clauses[] = 'ADD COLUMN `process_id` VARCHAR(64) DEFAULT NULL';
		}

		if ( ! $wpdb->get_var( "SHOW INDEX FROM `{$table}` WHERE Key_name = 'idx_transaction'" ) ) {
			$clauses[] = 'ADD INDEX `idx_transaction` (`transaction`(191))';
		}

		if ( $wpdb->get_var( "SHOW INDEX FROM `{$table}` WHERE Key_name = 'idx_transaction_unique'" ) ) {
			$clauses[] = 'DROP INDEX `idx_transaction_unique`';
		}

		if ( empty( $clauses ) ) {
			return true;
		}

		$applied = false !== $wpdb->query( sprintf( 'ALTER TABLE `%s` %s', $table, implode( ', ', $clauses ) ) );

		if ( ! $applied ) {
			// Without the column every claim fails, so a silent failure here
			// would reject otherwise valid payments with no way to diagnose it.
			error_log( sprintf( 'Bookit: failed to apply the payment claim schema to %s: %s', $table, $wpdb->last_error ) );
		}

		return $applied;
	}

	/**
	 * Claim a gateway transaction id for one appointment and write the row.
	 *
	 * The claim is written before the conflict is looked for, so a competing
	 * request has always written its own claim by the time either one checks:
	 * the locking read then blocks on it rather than missing it, which is what
	 * makes this safe without a unique constraint. Losing the race, hitting a
	 * deadlock, or hitting a lock-wait timeout all leave the row untouched.
	 *
	 * @since 2.6.0.4
	 *
	 * @param int    $appointment_id Appointment whose payment row is claiming the id.
	 * @param string $transaction_id Gateway transaction id being claimed.
	 * @param array  $data           Row data to write if the claim is won.
	 *
	 * @return bool Whether the claim was won.
	 */
	public static function claim_transaction( $appointment_id, $transaction_id, array $data ) {
		global $wpdb;

		$table            = esc_sql( self::_table() );
		$had_errors_shown = $wpdb->hide_errors();
		$won              = false;

		for ( $attempt = 0; $attempt < self::CLAIM_MAX_ATTEMPTS; $attempt++ ) {
			$data['process_id'] = uniqid( 'process_', true );

			if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
				break;
			}

			self::update( $data, array( 'appointment_id' => $appointment_id ) );

			if ( ! empty( $wpdb->last_error ) ) {
				$retry = self::is_retryable_error();
				$wpdb->query( 'ROLLBACK' );

				if ( $retry ) {
					continue;
				}

				break;
			}

			// process_id is rewritten every attempt, so the row always changes
			// when it exists: no rows touched means there was nothing to claim.
			if ( 1 !== (int) $wpdb->rows_affected ) {
				$wpdb->query( 'ROLLBACK' );
				break;
			}

			/**
			 * Fires inside the open claim transaction, after this request has
			 * written its claim and before it looks for a competing one.
			 *
			 * @since 2.6.0.4
			 *
			 * @param int    $appointment_id Appointment claiming the id.
			 * @param string $transaction_id Gateway transaction id being claimed.
			 * @param int    $attempt        Zero-based attempt number.
			 */
			do_action( 'bookit_payment_claim_written', $appointment_id, $transaction_id, $attempt );

			$conflicts = $wpdb->query(
				$wpdb->prepare(
					"SELECT `id` FROM `{$table}` WHERE `transaction` = %s AND `status` = %s AND `appointment_id` <> %d LIMIT 1 FOR UPDATE",
					$transaction_id,
					self::$completeStatus,
					$appointment_id
				)
			);

			// false is an error, 0 is a clean "nobody else holds it" -- the
			// two must not collapse, or a failed statement reads as a win.
			if ( false === $conflicts ) {
				$retry = self::is_retryable_error();
				$wpdb->query( 'ROLLBACK' );

				if ( $retry ) {
					continue;
				}

				break;
			}

			if ( $conflicts > 0 ) {
				$wpdb->query( 'ROLLBACK' );
				break;
			}

			$won = false !== $wpdb->query( 'COMMIT' );
			break;
		}

		if ( $had_errors_shown ) {
			$wpdb->show_errors();
		}

		return $won;
	}

	/**
	 * Whether the last database error is one the server expects the caller to
	 * retry, rather than a genuine failure.
	 *
	 * @since 2.6.0.4
	 *
	 * @return bool
	 */
	private static function is_retryable_error() {
		global $wpdb;

		// 1213 deadlock, 1205 lock wait timeout. A deadlock is rolled back
		// server-side; a lock wait timeout rolls back only the statement,
		// which is why the caller issues its own ROLLBACK either way.
		if ( $wpdb->dbh instanceof \mysqli ) {
			return in_array( mysqli_errno( $wpdb->dbh ), array( 1213, 1205 ), true );
		}

		return false !== stripos( (string) $wpdb->last_error, 'try restarting transaction' );
	}

	/**
	 * Find the completed payment row, if any, already holding a given
	 * gateway transaction id.
	 *
	 * @since 2.6.0.4
	 *
	 * @param string $transaction_id Gateway transaction id to look up.
	 *
	 * @return object|null
	 */
	public static function get_completed_by_transaction( $transaction_id ) {
		global $wpdb;

		$sql = $wpdb->prepare(
			sprintf(
				'SELECT * FROM `%s` WHERE `transaction` = %%s AND `status` = %%s ORDER BY `id` ASC LIMIT 1',
				esc_sql( self::_table() )
			),
			$transaction_id,
			self::$completeStatus
		);

		return $wpdb->get_row( $sql );
	}
}

<?php

namespace Bookit\Classes\Database;

use Bookit\Classes\Vendor\DatabaseModel;

class Customers extends DatabaseModel {

	/**
	 * Create Table
	 */
	public static function create_table() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$table_name  = self::_table();
		$primary_key = self::$primary_key;

		$sql = "CREATE TABLE {$table_name} (
			id INT UNSIGNED NOT NULL AUTO_INCREMENT,
			wp_user_id BIGINT(20),
			full_name VARCHAR(255) NOT NULL,
			email VARCHAR(255) NOT NULL,
			phone VARCHAR(255),
			PRIMARY KEY ({$primary_key}),
            INDEX `idx_wp_user_id` (`wp_user_id`)
		) {$wpdb->get_charset_collate()};";

		maybe_create_table( $table_name, $sql );
	}

	/**
	 * Get an unlinked customer whose submitted contact details all match.
	 *
	 * @since 2.6.0.2
	 *
	 * @param string $full_name Submitted customer full name.
	 * @param string $email     Submitted customer email.
	 * @param string $phone     Submitted customer phone.
	 *
	 * @return object|null
	 */
	public static function get_by_contact( $full_name, $email, $phone ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM %i WHERE `full_name` = %s AND `email` = %s AND `phone` = %s AND ( `wp_user_id` IS NULL OR `wp_user_id` = 0 ) LIMIT 1',
				self::_table(),
				$full_name,
				$email,
				$phone
			)
		);
	}

	/**
	 * Delete Customer
	 * Set customer appointments status = delete
	 * Update customer appointments notes
	 */
	public static function deleteCustomer( $id ) {
		global $wpdb;

		$wpdb->query( 'START TRANSACTION' );

		$customer              = self::get( 'id', $id );
		$customer->delete_date = gmdate( 'Y-m-d H:i:s' );

		$customerAppointments = Appointments::customer_appointments( $id );

		foreach ( $customerAppointments as $appointment ) {
			$notes                    = unserialize( $appointment->notes );
			$notes['delete_customer'] = $customer;

			$wpdb->update(
				Appointments::_table(),
				array(
					'notes' => serialize( $notes ),
					'status' => Appointments::$delete,
				),
				array( 'id' => $appointment->id )
			);
		}

		$sql = sprintf( 'DELETE FROM `%s` WHERE id = %d', esc_sql( self::_table() ), absint( $id ) );
		$wpdb->query( $wpdb->prepare( $sql ) );

		$wpdb->query( 'COMMIT' );
	}

	/**
	 * Save WP user if not exist
	 *
	 * @since 2.6.0 Let WordPress hash the password on insert so the account can authenticate later.
	 */
	public static function save_or_get_wp_user( $data ) {
		$is_exist_user = get_user_by( 'email', sanitize_email( $data['email'] ) );
		if ( $is_exist_user ) {
			return $is_exist_user->data->ID;
		}

		$user_data = array(
			'user_login' => sanitize_user( $data['email'] ),
			'user_pass'  => $data['password'],
			'first_name' => sanitize_text_field( $data['full_name'] ),
			'last_name'  => '',
			'user_email' => sanitize_email( $data['email'] ),
		);

		if ( empty( $user_data['first_name'] ) ) {
			$user_data['first_name'] = $user_data['user_login'];
		}

		if ( array_key_exists( 'role', $data ) && get_role( sanitize_text_field( $data['role'] ) ) != null ) {
			$user_data['role'] = sanitize_text_field( $data['role'] );
		}

		return wp_insert_user( $user_data );
	}
}

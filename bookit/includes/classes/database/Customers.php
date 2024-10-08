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

		$sql = sprintf( 'DELETE FROM %s WHERE id = %d', self::_table(), $id );
		$wpdb->query( $wpdb->prepare( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$wpdb->query( 'COMMIT' );
	}

	/** Save WP user if not exist **/
	public static function save_or_get_wp_user( $data ) {
		$is_exist_user = get_user_by( 'email', sanitize_email( $data['email'] ) );
		if ( $is_exist_user ) {
			return $is_exist_user->data->ID;
		}

		$user_data = array(
			'user_login' => sanitize_user( $data['email'] ),
			'user_pass'  => wp_hash_password( $data['password'] ),
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

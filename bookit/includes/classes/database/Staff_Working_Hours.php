<?php

namespace Bookit\Classes\Database;

use Bookit\Classes\Vendor\DatabaseModel;

class Staff_Working_Hours extends DatabaseModel {

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
			staff_id INT UNSIGNED NOT NULL,
			weekday INT,
			start_time TIME,
			end_time TIME,
			break_from TIME,
			break_to TIME,
			PRIMARY KEY ({$primary_key}),
            INDEX `idx_staff_id` (`staff_id`)
		) {$wpdb->get_charset_collate()};";

		maybe_create_table( $table_name, $sql );
	}

	/**
	 * Get a staff member's working hours for one weekday.
	 *
	 * @since 2.6.0.10
	 *
	 * @param int $staff_id The staff member ID.
	 * @param int $weekday  ISO-8601 weekday, 1 (Monday) to 7 (Sunday).
	 *
	 * @return object|null The working hours row, or null when none is stored.
	 */
	public static function get_by_staff_and_weekday( $staff_id, $weekday ) {
		global $wpdb;

		return $wpdb->get_row(
			$wpdb->prepare(
				'SELECT * FROM `' . esc_sql( self::_table() ) . '` WHERE staff_id = %d AND weekday = %d', // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$staff_id,
				$weekday
			)
		);
	}
}

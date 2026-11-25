<?php

namespace Bookit\Classes\Admin;

use Bookit\Classes\Base\Plugin;
use Bookit\Classes\Base\User;
use Bookit\Classes\Database\Appointments;
use Bookit\Classes\Database\Customers;
use Bookit\Classes\Database\Services;
use Bookit\Classes\Database\Staff;
use Bookit\Classes\Database\Staff_Services;
use Bookit\Classes\Database\Staff_Working_Hours;
use Bookit\Classes\Template;
use Bookit\Helpers\CleanHelper;

class StaffController extends DashboardController {

	private static function getCleanRules() {
		return array(
			'id'        => array( 'type' => 'intval' ),
			'limit'     => array( 'type' => 'intval' ),
			'offset'    => array( 'type' => 'intval' ),
			'email'     => array( 'type' => 'strval' ),
			'full_name' => array( 'type' => 'strval' ),
			'phone'     => array(
				'function' => array(
					'custom' => true,
					'name'   => 'custom_sanitize_phone',
				),
			),
		);
	}

	/**
	 * Keep a well-formed HH:MM(:SS) time, or null for an empty value or junk.
	 *
	 * @since 2.6.0.10
	 *
	 * @param mixed $value The submitted time.
	 *
	 * @return string|null
	 */
	private static function sanitize_time( $value ) {
		if ( ! $value || ! is_string( $value ) ) {
			return null;
		}

		return preg_match( '/^([01]?\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $value ) ? gmdate( 'H:i:s', strtotime( $value . ' UTC' ) ) : null;
	}
	/**
	 * Display Rendered Template
	 * @return bool|string
	 */
	public static function render() {

		$bookit_user = self::bookitUser();

		/** A staff member sees only their own row; the full list is admin-only. */
		if ( $bookit_user['is_staff'] ) {
			$staff = $bookit_user['staff'];
		} elseif ( current_user_can( 'manage_options' ) ) {
			$staff = Staff::get_all();
		} else {
			$staff = array();
		}

		$services = Services::get_all_short();
		// The user list only feeds the admin-only "link a WordPress account" control, so it is not sent to staff-level viewers.
		$wp_users = current_user_can( 'manage_options' )
			? get_users(
				array(
					'fields'       => array( 'ID', 'display_name', 'user_email' ),
					'role__not_in' => array( 'administrator' ),
				)
			)
			: array();

		$addons = array();
		$answer = array();
		$installed = Plugin::isAddonInstalledAndEnabled( self::$proAddon );
		/** if google calendar addon is installed */
		if ( Plugin::isAddonInstalledAndEnabled( self::$proAddon ) && has_filter( 'bookit_filter_connect_staff_google_calendar' ) ) {
			$addons[]       = Plugin::getAddonInfo( self::$proAddon );
			$gcFilterResult = apply_filters( 'bookit_filter_connect_staff_google_calendar', $staff );
			$staff          = $gcFilterResult['staff'];
			if ( array_key_exists( 'answer', $gcFilterResult ) ) {
				$answer = $gcFilterResult['answer'];
			}
		}
		/** if google calendar addon is installed | end */

		self::enqueue_styles_scripts();

		return Template::load_template(
			'dashboard/bookit-staff',
			array(
				'staff'    => self::parseStaff( $staff ),
				'services' => $services,
				'addons'   => $addons,
				'answer'   => $answer,
				'wp_users' => $wp_users,
				'page'     => __( 'Staff', 'bookit' ),
			),
			true
		);
	}


	/**
	 * Parsers and sanitizes staff data to array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int, array<string, mixed>> $staff Staff data to parse.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function parseStaff( $staff ) {
		$result = array();
		foreach ( $staff as $key => $employee ) {
			$item                   = (array) $employee;
			$item['full_name']      = stripslashes( $employee['full_name'] );
			$item['staff_services'] = json_decode( $employee['staff_services'] ?? '', true ) ?: [];
			$item['working_hours']  = json_decode( $employee['working_hours'] ?? '', true ) ?: [];

			// Ensure gc_token is always an object with auth_url property to prevent frontend errors.
			if (
				! isset( $item['gc_token'] )
				|| null === $item['gc_token']
				|| ! is_array( $item['gc_token'] )
			) {
				$item['gc_token'] = [
					'auth_url' => '',
				];
			} elseif ( ! isset( $item['gc_token']['auth_url'] ) ) {
				$item['gc_token']['auth_url'] = '';
			}

			array_push( $result, $item );
		}
		unset( $staff );
		return $result;
	}

	/**
	 * Get Staff with Pagination
	 */
	public static function get_staff() {

		$data = CleanHelper::cleanData( $_GET, self::getCleanRules() );

		if ( ! empty( $data['limit'] ) ) {
			$response['staff'] = Staff::get_paged( $data['limit'], $data['offset'] );
			$response['total'] = Staff::get_count();

			wp_send_json_success( $response );
		}

		array_walk(
			$response['staff'],
			function ( &$value, $key ) {
				$value['full_name'] = stripslashes( $value['full_name'] );
			}
		);

		wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
	}

	/**
	 * Validate staff fields
	 */
	public static function validate( $data ) {
		$errors = array();

		if ( isset( $data['phone'] ) && ( $data['phone'] !== '' || $data['phone'] === false ) ) {
			if ( ! preg_match( '/^((\+)?[0-9]{9,14})$/', $data['phone'] ) ) {
				$errors['phone'] = esc_html__( 'Please enter a valid phone number', 'bookit' );
			}
		}

		if ( ! $data['email'] ) {
			$errors['email'] = esc_html__( 'Email is required', 'bookit' );
		}

		if ( $data['email'] && ! is_email( $data['email'] ) ) {
			$errors['email'] = esc_html__( 'Not valid Email', 'bookit' );
		}

		if ( $data['full_name'] ) {
			if ( strlen( $data['full_name'] ) < 3 || strlen( $data['full_name'] ) > 50 ) {
				$errors['full_name'] = esc_html__( 'Full Name must be between 3 and 50 characters long', 'bookit' );
			}
		} elseif ( 'wp_user' != $data['object'] ) {
			$errors['full_name'] = esc_html__( 'Full Name is required.', 'bookit' );
		}

		if ( count( $errors ) > 0 ) {
			wp_send_json_error(
				array(
					'errors'  => $errors,
					'message' => esc_html__( 'Error occurred!', 'bookit' ),
				)
			);
		}
	}

	/**
	 * Create WordPress User from staff form
	 */
	public static function create_wp_user() {
		check_ajax_referer( 'bookit_save_item', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );
		self::validate( $data );

		if ( get_user_by( 'email', $data['email'] ) ) {
			wp_send_json_error(
				array(
					'errors'  => array( 'wp_email' => __( 'Wordpress User with such email already exist.', 'bookit' ) ),
					'message' => __( 'Error occurred!', 'bookit' ),
				)
			);
		}
		$data['role'] = User::$staff_role;

		$id     = Customers::save_or_get_wp_user( $data );
		$wpUser = get_user_by( 'ID', $id )->data;

		wp_send_json_success(
			array(
				'wp_user' => array(
					'ID'           => $wpUser->ID,
					'display_name' => $wpUser->display_name,
					'user_email'   => $wpUser->user_email,
				),
				'message' => __( 'Customer Saved!', 'bookit' ),
			)
		);
	}

	/**
	 * Save Staff
	 */
	public static function save() {
		check_ajax_referer( 'bookit_save_item', 'nonce' );

		if ( ! current_user_can( 'manage_bookit_staff' ) ) {
			return false;
		}

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );
		self::validate( $data );

		$id = ( ! empty( $data['id'] ) ) ? (int) $data['id'] : null;

		// Managing any staff member, linking a WordPress account and granting the
		// staff role are admin-only. A bookit_staff user may edit only their own
		// linked record; anyone else (e.g. an editor holding manage_bookit_staff) is refused.
		$is_admin    = current_user_can( 'manage_options' );
		$bookit_user = self::bookitUser();
		if ( ! $is_admin ) {
			$own_id = ( $bookit_user['is_staff'] && ! empty( $bookit_user['staff'] ) ) ? (int) $bookit_user['staff'][0]['id'] : 0;
			if ( ! $own_id || null === $id || $own_id !== $id ) {
				wp_send_json_error( array( 'message' => __( 'You are not allowed to manage this staff member.', 'bookit' ) ) );
			}
		}

		$staff_services = json_decode( stripslashes( $data['staff_services'] ?? '' ) );
		$working_hours  = json_decode( stripslashes( $data['working_hours'] ?? '' ) );

		// Build the staff row from known columns only, so a crafted request can't
		// write arbitrary fields (e.g. wp_user_id) to the staff record.
		$staff_row = array(
			'full_name' => stripslashes( $data['full_name'] ),
			'email'     => $data['email'],
			'phone'     => $data['phone'] ?? '',
		);

		$is_new          = ( null === $id );
		$prev_wp_user_id = $is_new ? 0 : (int) ( Staff::get( 'id', $id )->wp_user_id ?? 0 );

		// Linking a WordPress account is admin-only, and the link is re-checked only
		// when it changes, so editing a staff member whose linked user was deleted or
		// later gained manage_options is not refused for a metadata-only change. An
		// empty value detaches any existing link; a new link is never an admin account.
		$wp_user = false;
		if ( $is_admin ) {
			$wp_user_id = (int) ( $data['wp_user_id'] ?? 0 );
			if ( ! $wp_user_id ) {
				$staff_row['wp_user_id'] = null;
			} elseif ( $wp_user_id !== $prev_wp_user_id ) {
				$wp_user = get_user_by( 'ID', $wp_user_id );
				if ( ! $wp_user || user_can( $wp_user, 'manage_options' ) || is_super_admin( $wp_user->ID ) ) {
					wp_send_json_error(
						array(
							'errors'  => array( 'wp_user_id' => __( 'Please choose a valid user', 'bookit' ) ),
							'message' => __( 'Error occurred!', 'bookit' ),
						)
					);
				}
				$staff_row['wp_user_id'] = $wp_user_id;
			}
		}
		if ( $is_new ) {
			Staff::insert( $staff_row );
			$id = Staff::insert_id();
		} else {
			Staff::update( $staff_row, array( 'id' => $id ) );
		}

		// A working-hours row is updated only when it already belongs to this staff
		// member, so a submitted row id for another staff member matches nothing.
		foreach ( (array) $working_hours as $working_hour ) {
			$weekday = (int) ( $working_hour->weekday ?? 0 );
			if ( $weekday < 1 || $weekday > 7 ) {
				continue;
			}
			$start      = self::sanitize_time( $working_hour->start_time ?? null );
			$end        = self::sanitize_time( $working_hour->end_time ?? null );
			$break_from = self::sanitize_time( $working_hour->break_from ?? null );
			$break_to   = self::sanitize_time( $working_hour->break_to ?? null );

			// A working day ending at 00:00 runs until midnight.
			$closes = '00:00:00' === $end ? '24:00:00' : $end;

			// A working day and a break each need both endpoints in order, and the break must sit inside the day.
			if ( null === $start || null === $end || $start >= $closes ) {
				$start = null;
				$end   = null;
			}
			if ( null === $start || null === $break_from || null === $break_to || $break_from >= $break_to || $break_from < $start || $break_to > $closes ) {
				$break_from = null;
				$break_to   = null;
			}

			$row = array(
				'staff_id'   => $id,
				'weekday'    => $weekday,
				'start_time' => $start,
				'end_time'   => $end,
				'break_from' => $break_from,
				'break_to'   => $break_to,
			);
			// A new member's rows are inserted; an existing member's rows are updated
			// in place, keyed on both id and staff_id so a submitted id for another
			// member matches nothing. An existing member cannot insert extra rows,
			// which stops duplicate weekday entries.
			$row_id = (int) ( $working_hour->id ?? 0 );
			if ( $is_new ) {
				Staff_Working_Hours::insert( $row );
			} elseif ( $row_id ) {
				$where = array(
					'id'       => $row_id,
					'staff_id' => $id,
				);
				Staff_Working_Hours::update( $row, $where );
			}
		}

		// Only an admin rebuilds the staff/service links and their prices; a staff
		// member editing their own profile keeps whatever the admin assigned, so
		// they cannot attach themselves to extra services or set their own prices.
		if ( $is_admin ) {
			Staff_Services::delete_where( 'staff_id', $id );
			foreach ( (array) $staff_services as $staff_service ) {
				$service_id = (int) ( $staff_service->id ?? 0 );
				if ( ! $service_id || ! Services::get( 'id', $service_id ) ) {
					continue;
				}
				$service_row = array(
					'staff_id'   => $id,
					'service_id' => $service_id,
					'price'      => number_format( max( 0, (float) ( $staff_service->price ?? 0 ) ), 2, '.', '' ),
				);
				Staff_Services::insert( $service_row );
			}
		}

		// $wp_user is set only when the link just changed, so granting the staff role
		// here never reverts a role an admin later gave that user on purpose.
		if ( $wp_user ) {
			$wp_user->set_role( User::$staff_role );
		}

		// Always return staff data so frontend can use cleaned phone number.
		$staff = (array) Staff::get( 'id', $id );

		// Required for Google Calendar addon is installed.
		if ( Plugin::isAddonInstalledAndEnabled( self::$proAddon ) && has_filter( 'bookit_filter_connect_employee_google_calendar' ) ) {
			$staff = apply_filters( 'bookit_filter_connect_employee_google_calendar', $staff );
		}

		do_action( 'bookit_staff_saved', $id );

		wp_send_json_success(
			array(
				'id'      => $id,
				'staff'   => $staff,
				'message' => __( 'Staff Saved!', 'bookit' ),
			)
		);
	}

	/**
	 * Disconnect google calendar data from staff ( clean gc_token)
	 */
	public static function clean_gc_token() {
		check_ajax_referer( 'bookit_save_item', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );

		if ( ! isset( $data['id'] ) || empty( $data['id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
		}

		Staff::update( array( 'gc_token' => null ), array( 'id' => $data['id'] ) );
		$staff = (array) Staff::get( 'id', $data['id'] );

		/** if google calendar addon is installed */
		if ( Plugin::isAddonInstalledAndEnabled( self::$proAddon ) && has_action( 'bookit_filter_connect_employee_google_calendar' ) ) {
			$staff = apply_filters( 'bookit_filter_connect_employee_google_calendar', $staff );
		}
		/** if google calendar addon is installed | end */

		wp_send_json_success(
			array(
				'id'      => $staff['id'],
				'staff'   => $staff,
				'message' => __( 'Staff disconnected!', 'bookit' ),
			)
		);
	}

	/** Get Staff Assosiated data by id **/
	public static function get_assosiated_total_data_by_id() {
		check_ajax_referer( 'bookit_get_staff_assosiated_total_data', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );
		if ( empty( $data['id'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
		}

		$services     = Staff::get_staff_total_service( $data['id'] );
		$appointments = Appointments::get_total_active_assosiated_appointments( '', $data['id'] );

		$response = array(
			'total' => array(
				'services'     => $services,
				'appointments' => $appointments,
			),
		);
		wp_send_json_success( $response );
	}

	/**
	 * Delete the Staff
	 */
	public static function delete() {
		check_ajax_referer( 'bookit_delete_item', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$data = CleanHelper::cleanData( $_GET, self::getCleanRules() );

		if ( isset( $data['id'] ) ) {
			Staff::deleteStaff( $data['id'] );
			do_action( 'bookit_staff_deleted', $data['id'] );
			wp_send_json_success( array( 'message' => __( 'Staff Deleted!', 'bookit' ) ) );
		}
		wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
	}
}

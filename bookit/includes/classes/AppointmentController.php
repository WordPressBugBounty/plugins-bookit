<?php

namespace Bookit\Classes;

use Bookit\Classes\Admin\SettingsController;
use Bookit\Classes\Base\Plugin;
use Bookit\Classes\Base\User;
use Bookit\Classes\Database\Appointments;
use Bookit\Classes\Database\Customers;
use Bookit\Classes\Database\Services;
use Bookit\Classes\Database\Staff_Services;
use Bookit\Classes\Database\Staff_Working_Hours;
use Bookit\Classes\Vendor\Payments;
use Bookit\Helpers\CleanHelper;
use DateTimeZone;
use DateTime;
use Exception;


class AppointmentController {

	protected static $googleCalendarAddon = 'google-calendar';

	/**
	 * Check for Pro Addon active.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	protected static $proAddon = 'pro';

	private static function getCleanRules() {

		return array(
			'clear_price'     => array( 'type' => 'floatval' ),
			'user_id'         => array( 'type' => 'intval' ),
			'staff_id'        => array( 'type' => 'intval' ),
			'service_id'      => array( 'type' => 'intval' ),
			'start_timestamp' => array( 'type' => 'intval' ),
			'end_timestamp'   => array( 'type' => 'intval' ),
			'now_timestamp'   => array( 'type' => 'intval' ),
			'today_timestamp' => array( 'type' => 'intval' ),
			'date_timestamp'  => array( 'type' => 'intval' ),
			'start_time'      => array( 'type' => 'intval' ),
			'end_time'        => array( 'type' => 'intval' ),
			'email'           => array(
				'type'     => 'strval',
				'function' => array(
					'custom' => false,
					'name'   => 'sanitize_email',
				),
			),
			'payment_method'  => array( 'type' => 'strval' ),
			'full_name'       => array( 'type' => 'strval' ),
			'phone'           => array(
				'function' => array(
					'custom' => true,
					'name'   => 'custom_sanitize_phone',
				),
			),
		);
	}

	/**
	 * Convert UTC offset to timezone name.
	 *
	 * @since 2.5.2
	 *
	 * @param string $timezone_string The timezone string from wp_timezone_string().
	 *
	 * @return string The timezone name or an empty string if no match is found.
	 */
	private static function convert_offset_to_timezone_name( $timezone_string ) {
		// Check if it's a UTC offset (format: +HH:MM or -HH:MM).
		if ( preg_match( '/^([+-])(\d{2}):(\d{2})$/', $timezone_string, $matches ) ) {
			$sign           = ( $matches[1] === '-' ) ? -1 : 1;
			$hours          = (int) $matches[2];
			$minutes        = (int) $matches[3];
			$offset_seconds = $sign * ( $hours * 3600 + $minutes * 60 );

			// Try to find a timezone name based on the offset.
			$timezone_name = timezone_name_from_abbr( '', $offset_seconds, 0 );

			if ( $timezone_name !== false ) {
				return $timezone_name;
			}

			// If timezone_name_from_abbr() fails, search through timezone identifiers.
			$timezone_identifiers = DateTimeZone::listIdentifiers();
			$now                  = new DateTime( 'now', new DateTimeZone( 'UTC' ) );

			foreach ( $timezone_identifiers as $identifier ) {
				try {
					$tz = new DateTimeZone( $identifier );
					$offset = $tz->getOffset( $now );

					if ( $offset === $offset_seconds ) {
						return $identifier;
					}
				} catch ( Exception $e ) {
					continue;
				}
			}

			// If no match found, return an empty string.
			return '';
		}

		// Already a timezone name, return as is.
		return $timezone_string;
	}

	/**
	 * Validation
	 *
	 * @since 2.6.0 Booking identity is derived from the authenticated session in Registered mode.
	 * @since 2.6.0.7 Refuse a staff member who does not offer the chosen service or a disabled payment method, and return the price.
	 *
	 * @param $data
	 *
	 * @return string|null The price of the validated staff/service pair, or null when the pair is not linked.
	 */
	public static function validate( $data ) {
		$errors   = array();
		$settings = SettingsController::get_settings();

		if ( ! $data['staff_id'] ) {
			$errors['staff_id'] = __( 'No staff', 'bookit' );
		}
		if ( ! $data['service_id'] ) {
			$errors['service_id'] = __( 'No service', 'bookit' );
		}
		if ( ! $data['start_time'] ) {
			$errors['start_time'] = __( 'No start time', 'bookit' );
		}
		if ( ! $data['end_time'] ) {
			$errors['end_time'] = __( 'No end time', 'bookit' );
		}
		if ( ! $data['date_timestamp'] ) {
			$errors['date_timestamp'] = __( 'No date', 'bookit' );
		}

		$appointment = Appointments::checkAppointment( $data );

		if ( $appointment > 0 || ( $data['start_time'] && ! self::is_bookable_slot( $data ) ) ) {
			$errors['appointment'] = __( 'Selected Service Time is not available!', 'bookit' );
		}

		if ( $data['phone'] || false === $data['phone'] ) {
			if ( ! preg_match( '/^((\+)?[0-9]{8,14})$/', $data['phone'] ) ) {
				$errors['phone'] = __( 'Please enter a valid phone number', 'bookit' );
			}
		}

		if ( 'guest' == $settings['booking_type'] ) {
			if ( ! $data['email'] && ! $data['phone'] ) {
				$errors['phone'] = __( 'Please enter email or phone', 'bookit' );
			}
		}

		if ( ! $data['email'] ) {
			$errors['email'] = __( 'Please enter email', 'bookit' );
		}

		if ( $data['email'] && ! is_email( $data['email'] ) ) {
			$errors['email'] = __( 'Please enter your email in format youremail@example.com', 'bookit' );
		}

		if ( $data['full_name'] ) {
			if ( strlen( $data['full_name'] ) < 3 || strlen( $data['full_name'] ) > 25 ) {
				$errors['full_name'] = __( 'Full name must be between 3 and 25 characters long', 'bookit' );
			}
		} else {
			$errors['full_name'] = __( 'Please enter full name', 'bookit' );
		}

		$price = Staff_Services::get_service_price_by_staff( $data['service_id'], $data['staff_id'] );
		if ( null === $price ) {
			$errors['staff_service'] = __( 'Please choose correct staff and service', 'bookit' );
		} elseif ( (float) $data['clear_price'] !== (float) $price ) {
			$errors['clear_price'] = __( 'Price is incorrect', 'bookit' );
		}

		if ( (float) $price > 0 && empty( $settings['payments'][ $data['payment_method'] ]['enabled'] ) ) {
			$errors['payment_method'] = __( 'Please choose correct payment method', 'bookit' );
		}

		if ( count( $errors ) > 0 ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		return $price;
	}

	/**
	 * The current client's identifier for rate limiting. An IPv6 address is
	 * collapsed to its /64, since a client usually controls that whole range.
	 * Behind a proxy or CGNAT REMOTE_ADDR is shared, so a site that needs the real
	 * client address resolves it through the bookit_booking_rate_client_ip filter.
	 *
	 * @since 2.6.0.10
	 *
	 * @return string
	 */
	private static function rate_limit_ip() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		if ( '' !== $ip && false !== filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			$ip = bin2hex( substr( inet_pton( $ip ), 0, 8 ) ) . '::/64';
		}

		/**
		 * The identifier a public booking is rate-limited by. Defaults to the
		 * client IP, with an IPv6 address collapsed to its /64. A site behind a
		 * proxy or CGNAT can resolve the real client address here.
		 *
		 * @since 2.6.0.10
		 *
		 * @param string $ip The rate-limit identifier.
		 */
		return (string) apply_filters( 'bookit_booking_rate_client_ip', $ip );
	}

	/**
	 * Refuse a public booking once the current client has reached the limit.
	 * Called after validation, so only genuine booking attempts count; the
	 * read-and-increment is serialized with a named lock so parallel requests
	 * can't each pass a stale count. Staff and admins are not throttled.
	 *
	 * @since 2.6.0.10
	 */
	private static function enforce_booking_rate_limit() {
		if ( current_user_can( 'manage_bookit_staff' ) ) {
			return;
		}

		/**
		 * How many public bookings one client may create per window before the
		 * endpoint refuses further attempts. Zero or less disables the limit.
		 *
		 * @since 2.6.0.10
		 *
		 * @param int $limit Maximum bookings per window. Default 5.
		 */
		$limit = (int) apply_filters( 'bookit_booking_rate_limit', 5 );

		/**
		 * The rolling window the limit is counted over, in seconds. Kept separate
		 * from the limit so a site can retune either the count or the window on its
		 * own, for example 5 per day rather than 5 per hour.
		 *
		 * @since 2.6.0.10
		 *
		 * @param int $window Window length in seconds. Default one hour.
		 */
		$window = (int) apply_filters( 'bookit_booking_rate_window', HOUR_IN_SECONDS );

		$ip = self::rate_limit_ip();
		if ( $limit <= 0 || $window <= 0 || '' === $ip ) {
			return;
		}

		global $wpdb;
		$key  = 'bookit_rl_' . md5( $ip ) . '_' . (int) floor( time() / $window );
		$lock = Appointments::lock_name( 'bookit_rl_' . md5( $ip ) );

		/*
		 * Fail closed: if the lock can't be taken within the timeout the
		 * read-and-increment would run unserialized, so refuse rather than let
		 * parallel requests each pass a stale count. The lock is per client, so
		 * failing closed only ever delays that same client.
		 */
		$locked = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $lock, 2 ) );
		if ( 1 !== $locked ) {
			wp_send_json_error( array( 'message' => __( 'Too many booking attempts. Please try again later.', 'bookit' ) ) );
		}

		$count = (int) get_transient( $key );
		if ( $count >= $limit ) {
			$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
			wp_send_json_error( array( 'message' => __( 'Too many booking attempts. Please try again later.', 'bookit' ) ) );
		}
		set_transient( $key, $count + 1, $window );
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
	}

	/**
	 * Cancel front-end holds awaiting an immediate online payment that never
	 * completed, freeing their slots. Fired on a schedule.
	 *
	 * Cancels directly rather than through the status-change path on purpose, so no
	 * cancellation email is sent to the unverified address on an abandoned hold.
	 * Each cancellation fires `bookit_appointment_expired` so an integration that
	 * reserved the slot elsewhere, such as the Google Calendar add-on, can release
	 * it.
	 *
	 * @since 2.6.0.10
	 *
	 * @param int $ttl_seconds How long a pending online hold may keep its slot.
	 */
	public static function expire_abandoned_bookings( $ttl_seconds ) {
		foreach ( Appointments::get_expired_online_hold_ids( $ttl_seconds ) as $id ) {
			Appointments::cancel( $id );

			/**
			 * Fires after an abandoned booking is cancelled, so an integration such
			 * as the Google Calendar add-on can free the slot it reserved.
			 *
			 * @since 2.6.0.10
			 *
			 * @param int $id The cancelled appointment id.
			 */
			do_action( 'bookit_appointment_expired', $id );
		}
	}

	/**
	 * Whether the slot lies inside the staff member's working hours, clear of
	 * their break, and has not started yet.
	 *
	 * Booking times are the business's wall-clock times stored as if they were
	 * UTC (the booking app pins moment to GMT), so they are read with gmdate()
	 * and compared with the site's wall clock.
	 *
	 * @since 2.6.0.10
	 *
	 * @param array $data Booking data with staff_id, date_timestamp, start_time and end_time.
	 *
	 * @return bool True when the slot has positive length, lies in the future, inside working hours, on a valid slot grid and clear of the break.
	 */
	private static function is_bookable_slot( $data ) {
		// A slot must have positive length; a zero-duration service has nothing to book.
		if ( $data['end_time'] <= $data['start_time'] ) {
			return false;
		}

		$now = time() + wp_timezone()->getOffset( new DateTime() );
		if ( $data['start_time'] < $now ) {
			return false;
		}

		$hours = Staff_Working_Hours::get_by_staff_and_weekday( $data['staff_id'], (int) gmdate( 'N', $data['start_time'] ) );
		if ( ! $hours || ! $hours->start_time || ! $hours->end_time ) {
			return false;
		}

		$at = function ( $time ) use ( $data ) {
			return strtotime( $time . ' UTC', $data['date_timestamp'] );
		};

		$shift_start = $at( $hours->start_time );
		$shift_end   = $at( $hours->end_time );

		// A working day ending at 00:00 runs until midnight.
		if ( $at( '00:00' ) === $shift_end ) {
			$shift_end += DAY_IN_SECONDS;
		}

		if ( $data['start_time'] < $shift_start || $data['end_time'] > $shift_end ) {
			return false;
		}

		/*
		 * The start must sit on a real slot grid: the default view steps by the
		 * service duration from the shift start, the step-by-step view by the slot
		 * duration from midnight. Accept a start on either so a forged off-grid time
		 * (09:20 for a 60-minute service) is refused without rejecting a valid 09:15.
		 */
		$length    = $data['end_time'] - $data['start_time'];
		$settings  = SettingsController::get_settings();
		$slot_step = (int) ( $settings['time_slot_duration'] ?? 0 ) * MINUTE_IN_SECONDS;
		$on_grid   = 0 === ( $data['start_time'] - $shift_start ) % $length
			|| ( $slot_step > 0 && 0 === ( $data['start_time'] - $data['date_timestamp'] ) % $slot_step );
		if ( ! $on_grid ) {
			return false;
		}

		return ! ( $hours->break_from && $hours->break_to
			&& $data['start_time'] < $at( $hours->break_to )
			&& $data['end_time'] > $at( $hours->break_from ) );
	}

	/**
	 * Book Appointment
	 *
	 * @since 2.6.0 Require authentication and link the booking to the current user in Registered mode.
	 * @since 2.6.0.2 Derive the booking identity from the current session.
	 */
	public static function save() {
		$send_no_cache_headers = apply_filters( 'rest_send_nocache_headers', is_user_logged_in() );
		$nonce = '';
		if ( ! $send_no_cache_headers ) {
			$nonce                      = wp_create_nonce( 'bookit_nonce' );
			$_SERVER['HTTP_X_WP_NONCE'] = $nonce;
		}

		check_ajax_referer( 'bookit_book_appointment', 'nonce' );

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );

		// Payment status and origin are server-owned; drop any client value so a public booking can't be stored as paid or as admin-entered.
		unset( $data['payment_status'], $data['created_from'] );

		// A public booking always creates a new appointment; a submitted id would only skip that row in the conflict check.
		unset( $data['id'] );

		// Identify the booking from the current session.
		$data['user_id'] = get_current_user_id();

		$settings = SettingsController::get_settings();
		if ( 'registered' == $settings['booking_type'] ) {
			// Registered booking is tied to the authenticated visitor.
			if ( ! is_user_logged_in() ) {
				wp_send_json_error( array(
					'login_required' => true,
					'message'        => __( 'Please log in to complete your booking.', 'bookit' ),
				) );
			}

			$data['email'] = wp_get_current_user()->user_email;
		}

		/*
		 * The service sets how much time the price buys, so the length and day come
		 * from the server. With no service there is nothing to derive them from, so
		 * the booking fails closed.
		 */
		$service = Services::get( 'id', $data['service_id'] );
		if ( ! $service ) {
			wp_send_json_error( array( 'errors' => array( 'service_id' => __( 'No service', 'bookit' ) ) ) );
		}
		$data['end_time']       = $data['start_time'] + (int) $service->duration;
		$data['date_timestamp'] = $data['start_time'] - $data['start_time'] % DAY_IN_SECONDS;

		// The price is server-owned; never store the submitted value.
		$data['clear_price'] = self::validate( $data );
		if ( null === $data['clear_price'] ) {
			return;
		}

		// A genuine booking reached this point; count it toward the client's rate limit.
		self::enforce_booking_rate_limit();

		if ( empty( $data ) ) {
			wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
			die();
		}

		/*
		 * Without the lock a parallel booking could pass the same overlap check
		 * before either is stored, so the check runs again under it. It is taken
		 * after the rate limiter, whose own named lock would release it on MySQL
		 * before 5.7.5.
		 */
		$unavailable = array( 'errors' => array( 'appointment' => __( 'Selected Service Time is not available!', 'bookit' ) ) );
		if ( ! Appointments::lock_staff( $data['staff_id'] ) ) {
			wp_send_json_error( $unavailable );
		}

		try {
			if ( Appointments::checkAppointment( $data ) ) {
				wp_send_json_error( $unavailable );
			}

			$customer = CustomerController::get_customer( $data );

			$notes = array();
			if ( ! empty( $data['comment'] ) ) {
				$notes['comment'] = $data['comment'];
			}
			if ( $customer->email != $data['email'] ) {
				$notes['email'] = $data['email'];
			}
			if ( $customer->phone != $data['phone'] && ! empty( $data['phone'] ) ) {
				$notes['phone'] = $data['phone'];
			}
			if ( $customer->full_name != $data['full_name'] && ! empty( $data['full_name'] ) ) {
				$notes['full_name'] = $data['full_name'];
			}
			$data['customer_id'] = $customer->id;
			$data['notes']       = serialize( $notes );
			$data['status']      = Appointments::$pending;

			$id = Appointments::create_appointment( $data );
		} finally {
			Appointments::unlock_staff( $data['staff_id'] );
		}

		do_action( 'bookit_appointment_created', $id );

		$appointment          = (array) Appointments::get_full_appointment_by_id( $id );
		$appointment['token'] = $data['token'];

		/** if google calendar addon is installed */
		if ( Plugin::isAddonInstalledAndEnabled( self::$proAddon )
			&& has_action( 'bookit_google_calendar_create_appointment' ) ) {
			$appointment['customer_email'] = $notes['email'] ?? $appointment['customer_email'];
			$appointment['customer_phone'] = $notes['phone'] ?? $appointment['customer_phone'];
			do_action( 'bookit_google_calendar_create_appointment', $appointment );
		}
		/** if google calendar addon is installed | end */

		// Add timezone to the appointment.
		$appointment['timezone_name'] = self::convert_offset_to_timezone_name(
			wp_timezone_string()
		);

		$redirect_url = '';
		if ( ! is_null( $appointment['payment_method'] ) ) {
			$payments     = new Payments( $appointment );
			$redirect_url = $payments->redirect_url();
		}
		wp_send_json_success(
			array(
				'appointment'  => $appointment,
				'customer'     => $customer,
				'nonce'        => $nonce,
				'redirect_url' => $redirect_url,
				'message'      => __( 'Appointment Saved!', 'bookit' ),
			)
		);
	}

	/**
	 * Get Month Appointments
	 */
	public static function get_month_appointments() {
		check_ajax_referer( 'bookit_month_appointments', 'nonce' );

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );
		if ( ! empty( $data ) ) {
			$appointments = Appointments::month_appointments( $data );

			wp_send_json_success( $appointments );
		}

		wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
	}

	/**
	 * Get Day Appointments
	 */
	public static function get_day_appointments() {
		check_ajax_referer( 'bookit_day_appointments', 'nonce' );

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );

		if ( empty( $data ) ) {
			wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
			die();
		}

		$appointments = Appointments::day_appointments( $data );

		/** if google calendar addon is installed */
		if ( Plugin::isAddonInstalledAndEnabled( self::$proAddon )
			&& has_filter( 'bookit_google_calendar_get_events_by_date' ) ) {
			$gcBusyTimeSlots = apply_filters( 'bookit_google_calendar_get_events_by_date', $data );

			/** remove double values by id key */
			foreach ( $gcBusyTimeSlots as $key => $value ) {
				if ( array_search( $value->id, array_column( $appointments, 'id' ) ) !== false
					|| array_search( (string) $value->start_time, array_column( $appointments, 'start_time' ) ) ) {
					unset( $gcBusyTimeSlots[ $key ] );
				}
			}
			$appointments = array_merge( $gcBusyTimeSlots, $appointments );
		}
		/** if google calendar addon is installed | end */
		wp_send_json_success( $appointments );
	}

	/**
	 * Check is Appointment time free
	 */
	public static function is_free_appointment() {
		check_ajax_referer( 'bookit_is_free_appointment', 'nonce' );

		$data = CleanHelper::cleanData( $_POST, self::getCleanRules() );
		if ( empty( $data['staff_id'] ) || empty( $data['service_id'] ) || empty( $data['date_timestamp'] )
			|| empty( $data['start_time'] ) || empty( $data['end_time'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Error occurred!', 'bookit' ) ) );
		}

		$appointment = Appointments::checkAppointment( $data );
		wp_send_json_success( array( 'is_free' => false ) );
	}
}

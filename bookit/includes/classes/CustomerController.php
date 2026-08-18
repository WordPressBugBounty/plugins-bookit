<?php

namespace Bookit\Classes;

use Bookit\Classes\Base\User;
use Bookit\Classes\Database\Customers;

class CustomerController {

	/**
	 * Appointment Customer
	 *
	 * @since 2.6.0 Resolve the customer from the authenticated session instead of request-supplied credentials.
	 * @since 2.6.0.2 Resolve an existing customer from the session identity.
	 * @since 2.6.0.2 Reuse an unlinked customer when the submitted contact fully matches.
	 *
	 * @param array $data
	 *
	 * @return object customer
	 */
	public static function get_customer( $data ) {
		$id = null;

		if ( ! empty( $data['user_id'] ) ) {
			$customer = Customers::get( 'wp_user_id', $data['user_id'] );
			$id       = $customer ? $customer->id : null;
		}

		if ( ! $id ) {
			$customer = self::match_guest_customer( $data );
			$id       = $customer ? $customer->id : null;
		}

		if ( ! $id ) {
			$id = self::save( $data );
		}

		self::maybe_update( $id, array(
				'wp_user_id' => $data['user_id'],
				'phone'      => $data['phone'],
			) );

		return Customers::get( 'id', $id );
	}

	/**
	 * Authenticate a returning customer through WordPress and return a
	 * session-ready payload so booking can continue without leaving the page.
	 *
	 * @since 2.6.0
	 */
	public static function login() {
		check_ajax_referer( 'bookit_login', 'nonce' );

		$creds = array(
			'user_login'    => sanitize_text_field( wp_unslash( $_POST['email'] ?? '' ) ),
			'user_password' => (string) ( $_POST['password'] ?? '' ),
			'remember'      => true,
		);

		// Make the just-issued session available to wp_create_nonce() in this request.
		add_action( 'set_logged_in_cookie', array( self::class, 'sync_logged_in_cookie' ) );

		$user = wp_signon( $creds );

		if ( is_wp_error( $user ) ) {
			// Keep credential failures generic so the form can't reveal which emails exist.
			$generic  = array( 'invalid_username', 'invalid_email', 'incorrect_password' );
			$message  = array_intersect( $generic, $user->get_error_codes() )
				? __( 'The email or password you entered is incorrect.', 'bookit' )
				: wp_strip_all_tags( $user->get_error_message() );
			wp_send_json_error( array( 'message' => $message ) );
		}

		wp_set_current_user( $user->ID );

		wp_send_json_success( self::auth_payload( $user ) );
	}

	/**
	 * Register a new customer account, sign them in, and return a session-ready
	 * payload so booking can continue without leaving the page.
	 *
	 * @since 2.6.0
	 */
	public static function register() {
		check_ajax_referer( 'bookit_register', 'nonce' );

		$email     = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
		$full_name = sanitize_text_field( wp_unslash( $_POST['full_name'] ?? '' ) );
		$password  = (string) ( $_POST['password'] ?? '' );
		$confirm   = (string) ( $_POST['password_confirmation'] ?? '' );

		$errors = array();
		if ( ! is_email( $email ) ) {
			$errors['email'] = __( 'Please enter your email in format youremail@example.com', 'bookit' );
		}
		if ( strlen( $full_name ) < 3 || strlen( $full_name ) > 25 ) {
			$errors['full_name'] = __( 'Full name must be between 3 and 25 characters long', 'bookit' );
		}
		if ( empty( $password ) ) {
			$errors['password'] = __( 'Please enter a password', 'bookit' );
		} elseif ( false !== strpos( $password, '\\' ) ) {
			$errors['password'] = __( "Passwords may not contain the character '\\'", 'bookit' );
		} elseif ( $password !== $confirm ) {
			$errors['password_confirmation'] = __( 'Please enter the same password in both password fields', 'bookit' );
		}
		if ( $errors ) {
			wp_send_json_error( array( 'errors' => $errors ) );
		}

		if ( get_user_by( 'email', $email ) ) {
			wp_send_json_error( array( 'message' => __( 'An account with this email already exists. Please log in.', 'bookit' ) ) );
		}

		$user_id = Customers::save_or_get_wp_user( array(
			'email'     => $email,
			'password'  => $password,
			'full_name' => $full_name,
			'role'      => User::$customer_role,
		) );

		// Never create or authenticate a privileged account through the booking form.
		if ( is_wp_error( $user_id ) || ! $user_id || user_can( $user_id, 'administrator' ) ) {
			wp_send_json_error( array( 'message' => __( 'Could not create your account. Please try again.', 'bookit' ) ) );
		}

		// Make the just-issued session available to wp_create_nonce() in this request.
		add_action( 'set_logged_in_cookie', array( self::class, 'sync_logged_in_cookie' ) );

		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );

		wp_send_json_success( self::auth_payload( get_user_by( 'id', $user_id ) ) );
	}

	/**
	 * Expose the freshly-set login cookie to the rest of this request so nonces
	 * are minted against the new session token (they are verified on the next
	 * request, which carries that same cookie).
	 *
	 * @since 2.6.0
	 *
	 * @param string $logged_in_cookie
	 */
	public static function sync_logged_in_cookie( $logged_in_cookie ) {
		$_COOKIE[ LOGGED_IN_COOKIE ] = $logged_in_cookie;
	}

	/**
	 * Session-ready payload shared by login()/register(): the current user plus
	 * freshly minted nonces for the authenticated session.
	 *
	 * @since 2.6.0
	 *
	 * @param \WP_User $user
	 *
	 * @return array
	 */
	private static function auth_payload( $user ) {
		return array(
			'user' => array(
				'ID'           => $user->ID,
				'display_name' => $user->display_name,
				'user_email'   => $user->user_email,
				'customer'     => Customers::get( 'wp_user_id', $user->ID ),
				'nonce'        => wp_create_nonce( 'bookit_book_appointment' ),
			),
			'nonces' => Nonces::get_frontend_nonces(),
		);
	}

	/**
	 * Reuse an existing unlinked customer only when every submitted contact
	 * field matches, so repeat guest bookings do not create duplicate records.
	 *
	 * @since 2.6.0.2
	 *
	 * @param array $data Cleaned booking request data.
	 *
	 * @return object|null
	 */
	private static function match_guest_customer( $data ) {
		// Only anonymous bookings are matched; a session always resolves to its own customer.
		if ( ! empty( $data['user_id'] ) ) {
			return null;
		}

		if ( empty( $data['full_name'] ) || empty( $data['email'] ) || empty( $data['phone'] ) ) {
			return null;
		}

		return Customers::get_by_contact( $data['full_name'], $data['email'], $data['phone'] );
	}

	/**
	 * Save Customer
	 *
	 * @param array $data
	 *
	 * @return int
	 */
	private static function save( $data ) {
		$insert = array(
			'full_name'  => $data['full_name'],
			'wp_user_id' => $data['user_id'],
			'email'      => $data['email'],
			'phone'      => $data['phone'],
		);
		Customers::insert( $insert );

		return Customers::insert_id();
	}

	/**
	 * Update Customer if appear new data
	 *
	 * @param int   $id
	 * @param array $data
	 *
	 * @return void
	 */
	private static function maybe_update( $id, $data ) {
		$customer = Customers::get( 'id', $id );

		if ( $customer->wp_user_id || $customer->wp_user_id == $data['wp_user_id'] ) {
			unset( $data['wp_user_id'] );
		}
		if ( $customer->phone ) {
			unset( $data['phone'] );
		}

		if ( $data ) {
			Customers::update( $data, array( 'id' => $id ) );
		}
	}
}

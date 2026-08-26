<?php

namespace Bookit\Classes\Payments;

use Bookit\Classes\Database\Payments as PaymentDb;
use Bookit\Helpers\SerializationHelper;

class PayPal {

	public $url;
	public $currency_code;
	public $email;
	public $return_url;
	public $invoice;
	public $amount;
	public $item_name;
	public $item_number;
	public $user_email;

	/**
	 * PayPal constructor.
	 *
	 * @param int $amount
	 * @param int $invoice
	 * @param string $item_name
	 * @param string $item_number
	 * @param string $user_email
	 * @param string $return_url
	 */
	public function __construct( $amount = 10, $invoice = 0, $item_name = '', $item_number = '', $user_email = '', $return_url = '' ) {
		$settings = get_option( 'bookit_settings' );
		$payments = $settings['payments'];

		if ( ! empty( $payments['paypal'] ) && ! empty( $payments['paypal']['enabled'] ) ) {
			$paypal = $payments['paypal'];

			$this->url           = ( 'live' == $paypal['mode'] ) ? 'www.paypal.com' : 'www.sandbox.paypal.com';
			$this->currency_code = $settings['currency'];
			$this->email         = $paypal['email'];
			$this->invoice       = $invoice;
			$this->amount        = $amount;
			$this->item_name     = $item_name;
			$this->item_number   = $item_number;
			$this->user_email    = $user_email;
			$this->return_url    = apply_filters( 'bookit_paypal_return_url', empty( $return_url ) ? home_url() : $return_url );
		}
	}

	/**
	 * Generate Payment URL
	 * @return string
	 */
	public function generate_payment_url() {
		$get_params = array(
			'cmd'           => '_xclick',
			'business'      => $this->email,
			'no_shipping'   => 1,
			'no_note'       => 1,
			'currency_code' => strtoupper( $this->currency_code ),
			'bn'            => 'PP%2dBuyNowBF',
			'charset'       => 'UTF%2d8',
			'item_name'     => $this->item_name,
			'item_number'   => $this->item_number,
			'invoice'       => $this->invoice,
			'return'        => $this->return_url,
			'email'         => $this->user_email,
			'rm'            => 2,
			'amount'        => $this->amount,
			'notify_url'    => get_home_url() . '/?stm_bookit_check_ipn=1',
		);

		$url = 'https://' . $this->url . '/cgi-bin/webscr?' . http_build_query( $get_params );

		return $url;
	}

	/**
	 * Check IPN Response
	 *
	 * @since 2.6.0.3 Verify the notification against the stored order before trusting it.
	 *
	 * @param array $ipn_response
	 */
	public function check_payment( $ipn_response ) {
		$paypal_adress  = 'https://' . $this->url . '/cgi-bin/webscr';
		$validate_ipn   = array( 'cmd' => '_notify-validate' );
		$validate_ipn   += stripslashes_deep( $ipn_response );

		$params = array(
			'body'        => $validate_ipn,
			'sslverify'   => true,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'paypal-ipn/',
		);

		$response = wp_safe_remote_post( $paypal_adress, $params );

		$verified = ! is_wp_error( $response )
			&& $response['response']['code'] >= 200 && $response['response']['code'] < 300
			&& strstr( $response['body'], 'VERIFIED' );

		if ( $verified ) {
			$this->apply_verified_notification( $ipn_response );
		}

		header( 'HTTP/1.1 200 OK' );
		exit;
	}

	/**
	 * Complete, reject, or leave pending the payment for a PayPal-verified
	 * notification. A missing invoice, or one already marked complete
	 * (duplicate delivery), is left untouched.
	 *
	 * @since 2.6.0.3
	 *
	 * @param array $ipn_response
	 */
	private function apply_verified_notification( $ipn_response ) {
		if ( empty( $ipn_response['invoice'] ) ) {
			return;
		}

		$payment = PaymentDb::get( 'appointment_id', $ipn_response['invoice'] );

		if ( empty( $payment ) || PaymentDb::$completeStatus === $payment->status ) {
			return;
		}

		$data = array(
			'transaction' => $ipn_response['txn_id'],
			'notes'       => serialize( $ipn_response ),
			'updated_at'  => wp_date( 'Y-m-d H:i:s' ),
		);

		$where = array( 'id' => (int) $payment->id );

		if ( $this->order_mismatched( $ipn_response, $payment->total ) ) {
			$data['status'] = PaymentDb::$rejectedStatus;
			PaymentDb::update( $data, $where );

			return;
		}

		if ( $this->matches_stored_order( $ipn_response, $payment ) ) {
			$data['status']  = PaymentDb::$completeStatus;
			$data['paid_at'] = wp_date( 'Y-m-d H:i:s' );

			PaymentDb::update( $data, $where );

			do_action( 'bookit_payment_complete', $ipn_response['invoice'] );

			return;
		}

		if ( isset( $ipn_response['payment_status'] ) && 'Pending' === $ipn_response['payment_status'] ) {
			$data['status'] = PaymentDb::$defaultStatus;
			PaymentDb::update( $data, $where );

			return;
		}

		$data['status'] = PaymentDb::$rejectedStatus;
		PaymentDb::update( $data, $where );
	}

	/**
	 * Confirm a notification matches the stored order for its invoice, and
	 * was not already used to complete a different payment.
	 *
	 * @since 2.6.0.3
	 *
	 * @param array $ipn_response
	 * @param object $payment Row from wp_bookit_payments.
	 * @return bool
	 */
	private function matches_stored_order( $ipn_response, $payment ) {
		if ( empty( $ipn_response['txn_id'] ) ) {
			return false;
		}

		$used_txn = PaymentDb::get( 'transaction', $ipn_response['txn_id'] );
		if ( ! empty( $used_txn ) && PaymentDb::$completeStatus === $used_txn->status ) {
			return false;
		}

		$status_ok = isset( $ipn_response['payment_status'] ) && 'Completed' === $ipn_response['payment_status'];

		return $status_ok && ! $this->order_mismatched( $ipn_response, $payment->total );
	}

	/**
	 * Whether a rejected payment was rejected specifically because the
	 * stored IPN's currency, receiver, or amount disagreed with the order
	 * — as opposed to any other rejection reason (e.g. PayPal itself
	 * denying/voiding the transaction).
	 *
	 * @since 2.6.0.3
	 *
	 * @param object $payment Row from wp_bookit_payments (needs ->total and ->notes).
	 * @return bool
	 */
	public function is_payment_mismatch( $payment ) {
		if ( empty( $payment->notes ) ) {
			return false;
		}

		$ipn_response = SerializationHelper::safe_unserialize( trim( $payment->notes ) );
		if ( false === $ipn_response ) {
			return false;
		}

		return $this->order_mismatched( $ipn_response, $payment->total );
	}

	/**
	 * Whether the notification's currency, receiver, or amount disagree
	 * with the stored order, regardless of payment status.
	 *
	 * @since 2.6.0.3
	 *
	 * @param array $ipn_response
	 * @param string|float $total
	 * @return bool
	 */
	private function order_mismatched( $ipn_response, $total ) {
		$currency_ok = isset( $ipn_response['mc_currency'] ) && strtoupper( $ipn_response['mc_currency'] ) === strtoupper( (string) $this->currency_code );
		$receiver_ok = $this->matches_configured_receiver( $ipn_response );
		$amount_ok   = isset( $ipn_response['mc_gross'] ) && $this->amounts_match( $ipn_response['mc_gross'], $total );

		return ! $currency_ok || ! $receiver_ok || ! $amount_ok;
	}

	/**
	 * Confirm the IPN's receiver/business matches the configured PayPal account.
	 *
	 * @since 2.6.0.3
	 *
	 * @param array $ipn_response
	 * @return bool
	 */
	private function matches_configured_receiver( $ipn_response ) {
		$receiver = ! empty( $ipn_response['receiver_email'] ) ? $ipn_response['receiver_email'] : ( $ipn_response['business'] ?? '' );

		return ! empty( $receiver ) && strtolower( trim( $receiver ) ) === strtolower( trim( (string) $this->email ) );
	}

	/**
	 * Strict decimal comparison so a mismatched amount can't slip through as
	 * a float-precision false positive.
	 *
	 * @since 2.6.0.3
	 *
	 * @param string|float $mc_gross
	 * @param string|float $total
	 * @return bool
	 */
	private function amounts_match( $mc_gross, $total ) {
		return number_format( (float) $mc_gross, 2, '.', '' ) === number_format( (float) $total, 2, '.', '' );
	}
}

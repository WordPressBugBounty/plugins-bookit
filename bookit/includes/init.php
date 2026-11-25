<?php

use Bookit\Plugin;

/* Load Textdomain */
if ( ! is_textdomain_loaded( 'bookit' ) ) {
    load_plugin_textdomain( 'bookit', false, 'bookit/lang' );
}

/**
 * Init Ajax Actions
 */
add_action(
	'init',
	function () {
		\Bookit\Classes\AjaxActions::init();
		if ( ! empty( $_GET['stm_bookit_check_ipn'] ) ) {

			$paypal = new \Bookit\Classes\Payments\PayPal();
			$paypal->check_payment( $_REQUEST );
		}
	}
);

/**
 * Init Ajax Actions
 */
add_action('init', function () {
    \Bookit\Classes\AjaxActions::init();
});

/**
 * Schedule and run the hourly cleanup that frees slots held by abandoned,
 * unpaid online bookings.
 */
add_action(
	'init',
	function () {
		if ( ! wp_next_scheduled( 'bookit_expire_abandoned_bookings' ) ) {
			wp_schedule_event( time(), 'hourly', 'bookit_expire_abandoned_bookings' );
		}
	}
);
add_action(
	'bookit_expire_abandoned_bookings',
	function () {
		/**
		 * How long an abandoned, unpaid online booking may hold its slot before
		 * the cleanup frees it, in seconds. Zero or less disables the cleanup.
		 *
		 * @since 2.6.0.10
		 *
		 * @param int $ttl Time-to-live in seconds. Default one hour.
		 */
		$ttl = (int) apply_filters( 'bookit_abandoned_booking_ttl', HOUR_IN_SECONDS );
		if ( $ttl <= 0 ) {
			return;
		}
		\Bookit\Classes\AppointmentController::expire_abandoned_bookings( $ttl );
	}
);

/**
 * Init Admin Classes
 */
if ( is_admin() ) {
	\Bookit\Classes\Vendor\BookitUpdates::init();
	\Bookit\Classes\Admin\AdminMenu::init();
	\Bookit\Classes\Customization::init();
}

/**
 * Init Classes
 */
\Bookit\Classes\BookitController::init();
\Bookit\Classes\Notifications::init();

/**
 * Init Widgets
 */
add_action( 'vc_after_set_mode', [new \Bookit\Widgets\VisualComposerWidget(), 'load'] );
add_action( 'plugins_loaded', function() {
	if ( defined('ELEMENTOR_VERSION') && version_compare( ELEMENTOR_VERSION, '2.6.7', '>=' ) ) {
		require_once( BOOKIT_INCLUDES_PATH . '/widgets/ElementorWidget.php' );
	}
});

/** if this is staff, hide dashboard menu link */
add_action( 'admin_init', function(){
	if ( \Bookit\Classes\Base\User::getUserData()['is_staff'] ) {
		remove_menu_page( 'index.php' );
	}
} );

/**
 * Register and load the service provider for loading the plugin.
 *
 * @since 2.5.0
 */
function bookit_load() {
	// Last file that needs to be loaded manually.
	require_once dirname( BOOKIT_FILE ) . '/src/Bookit/Plugin.php';

	// Load the plugin, autoloading happens here.
	$plugin = new Plugin();
	$plugin->boot();
}

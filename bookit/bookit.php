<?php
/**
 * Plugin Name: Booking Calendar | Appointment Booking | Bookit
 * Plugin URI: https://bookitwp.com/
 * Description: Booking Appointments Calendar. You can easily realize Booking Appointments with this plugin.
 * Author: The Events Calendar
 * Author URI: https://theeventscalendar.com/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bookit
 * Version: 2.5.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BOOKIT_VERSION', '2.5.1' );
define( 'BOOKIT_DB_VERSION', '2.2.5' );
define( 'BOOKIT_FILE', __FILE__ );
define( 'BOOKIT_PATH', dirname( BOOKIT_FILE ) );
define( 'BOOKIT_INCLUDES_PATH', BOOKIT_PATH . '/includes/' );
define( 'BOOKIT_CLASSES_PATH', BOOKIT_INCLUDES_PATH . 'classes/' );
define( 'BOOKIT_URL', plugin_dir_url( BOOKIT_FILE ) );

require_once BOOKIT_PATH . '/includes/autoload.php';

if ( ! function_exists( 'bookit_fs' ) ) {
	// Create a helper function for easy SDK access.
	function bookit_fs() {
		global $bookit_fs;

		if ( ! isset( $bookit_fs ) ) {
			// Include Freemius SDK.
			if ( file_exists( BOOKIT_PATH . '/freemius/start.php' ) ) {
				require_once BOOKIT_PATH . '/freemius/start.php';
			}

			$bookit_fs = fs_dynamic_init( array(
					'id'             => '8486',
					'slug'           => 'bookit',
					'type'           => 'plugin',
					'public_key'     => 'pk_2cc14bc8c7ec47520d21f0b7d99e7',
					'is_premium'     => false,
					'has_addons'     => true,
					'has_paid_plans' => false,
					'menu'           => array(
						'slug'       => 'bookit',
						'first-path' => 'admin.php?page=bookit-settings',
						'account'    => true,
						'contact'    => true,
						'support'    => false,
						'addons'     => false,
					),
				) );
		}

		return $bookit_fs;
	}

	// Init Freemius.
	bookit_fs();
	// Signal that SDK was initiated.
	do_action( 'bookit_fs_loaded' );

	call_user_func( array( 'Bookit\Classes\Base\Plugin', 'run' ) );
}

function my_after_upgrade_addon_sync( $prev_version, $new_version ) {
	if ( '2.1.7' === $new_version ) {
		// The true purges the cache.
		bookit_fs()->get_addons( true );
	}
}

$bookit_fs = bookit_fs();
$bookit_fs->add_action( 'plugin_version_update', 'my_after_upgrade_addon_sync' );
$bookit_fs->add_action( 'after_uninstall', array( \Bookit\Classes\Base\Plugin::class, 'uninstall' ) );

/**
 * remove duplicates 'contact us'
 * and 'account' for bookit lower than 2.0.0
 */
\Bookit\Classes\Admin\SettingsController::removeBookitProFreemiusSubMenuDuplicate();
\Bookit\Classes\Admin\SettingsController::removeBookitContactUsForFreeVersion();

add_action( 'plugins_loaded', 'bookit_load', 0 );
<?php
/**
 * Plugin Name:       Sophi for WordPress
 * Plugin URI:        https://github.com/10up/sophi-for-wordpress
 * Description:       A brief description of the plugin.
 * Version:           0.1.0
 * Requires at least: 4.9
 * Requires PHP:      7.2
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sophi-wp
 * Domain Path:       /languages
 *
 * @package           SophiWP
 */

// Useful global constants.
define( 'SOPHI_WP_VERSION', '0.1.0' );
define( 'SOPHI_WP_URL', plugin_dir_url( __FILE__ ) );
define( 'SOPHI_WP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SOPHI_WP_INC', SOPHI_WP_PATH . 'includes/' );

// Include files.
require_once SOPHI_WP_INC . 'functions/core.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\SophiWP\Core\activate' );
register_deactivation_hook( __FILE__, '\SophiWP\Core\deactivate' );

// Bootstrap.
SophiWP\Core\setup();

// Require Composer autoloader if it exists.
if ( file_exists( SOPHI_WP_PATH . 'vendor/autoload.php' ) ) {
	require_once SOPHI_WP_PATH . 'vendor/autoload.php';
}

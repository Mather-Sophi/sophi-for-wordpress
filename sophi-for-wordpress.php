<?php
/**
 * Plugin Name:       Sophi for WordPress
 * Plugin URI:        https://github.com/10up/sophi-for-wordpress
 * Description:       WordPress VIP-compatible plugin for the Sophi.io Curator service.
 * Version:           0.1.0
 * Requires at least: 5.6
 * Requires PHP:      7.4
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
require_once SOPHI_WP_INC . 'functions/settings.php';
require_once SOPHI_WP_INC . 'functions/tracking.php';

// Activation/Deactivation.
register_activation_hook( __FILE__, '\SophiWP\Core\activate' );
register_deactivation_hook( __FILE__, '\SophiWP\Core\deactivate' );

add_action(
	'init',
	function() {
		if ( apply_filters( 'sophi_available', is_ssl() ) ) {
			// Bootstrap.
			SophiWP\Core\setup();
			SophiWP\Settings\setup();
			SophiWP\Tracking\setup();
		} else {
			add_action(
				'admin_notices',
				function() {
					?>
				<div class="notice notice-error">
						<p><?php esc_html_e( 'Sophi requires HTTPS. Please install SSL to your site and try again.', 'sophi-wp' ); ?></p>
				</div>
					<?php
				}
			);
		}
	}
);

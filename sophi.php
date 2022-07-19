<?php
/**
 * Plugin Name:       Sophi
 * Plugin URI:        https://github.com/globeandmail/sophi-for-wordpress
 * Description:       WordPress VIP-compatible plugin for the Sophi.io Site Automation service.
 * Version:           1.1.4-dev
 * Requires at least: 5.6
 * Requires PHP:      7.4
 * Author:            10up
 * Author URI:        https://10up.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sophi-wp
 *
 * @package           SophiWP
 */

// Useful global constants.
define( 'SOPHI_WP_VERSION', '1.1.3' );
define( 'SOPHI_WP_URL', plugin_dir_url( __FILE__ ) );
define( 'SOPHI_WP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SOPHI_WP_INC', SOPHI_WP_PATH . 'includes/' );

// phpcs:disable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

// Require Composer autoloader if it exists.
if ( file_exists( SOPHI_WP_PATH . 'vendor/autoload.php' ) ) {
	require_once SOPHI_WP_PATH . 'vendor/autoload.php';
}

// Include files.
require_once SOPHI_WP_INC . 'functions/utils.php';
require_once SOPHI_WP_INC . 'functions/core.php';
require_once SOPHI_WP_INC . 'functions/settings.php';
require_once SOPHI_WP_INC . 'functions/tracking.php';
require_once SOPHI_WP_INC . 'functions/content-sync.php';
require_once SOPHI_WP_INC . 'functions/blocks.php';
require_once SOPHI_WP_INC . 'functions/post-type.php';

// phpcs:enable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

// Activation/Deactivation.
register_activation_hook( __FILE__, '\SophiWP\Core\activate' );
register_deactivation_hook( __FILE__, '\SophiWP\Core\deactivate' );

add_action(
	'init',
	function() {
		/**
		 * Filter whether Sophi is available for the current site.
		 * By default, Sophi is only available for site with HTTPS enabled.
		 *
		 * @since 1.0.0
		 * @hook sophi_available
		 *
		 * @param {bool} $available Whether Sophi should be enabled.
		 *
		 * @return {bool} Whether Sophi should be enabled.
		 */
		if ( apply_filters( 'sophi_available', is_ssl() ) ) {
			// Bootstrap.
			SophiWP\Core\setup();
			SophiWP\Settings\setup();
			SophiWP\PostType\setup();

			if ( ! SophiWP\Utils\is_configured() ) {
				return add_action( 'admin_notices', 'sophi_setup_notice' );
			}

			SophiWP\ContentSync\setup();
			SophiWP\Tracking\setup();
			SophiWP\Blocks\setup();
			( new SophiWP\SiteAutomation\Services() )->register();

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				try {
					\WP_CLI::add_command( 'sophi', 'SophiWP\Command' );
				} catch ( \Exception $e ) {
					error_log( $e->getMessage() ); // phpcs:ignore
				}
			}

			/**
			 * Fires after Sophi has been loaded.
			 *
			 * @since 1.0.0
			 * @hook sophi_loaded
			 */
			do_action( 'sophi_loaded' );
		} else {
			add_action( 'admin_notices', 'sophi_https_notice' );
		}
	}
);

/**
 * Method to cleanup settings before setting the new version.
 *
 * @param string $plugin Full path to the plugin's main file.
 */
function sophi_remove_stale_data( $plugin ) {
	/** Return if the plugin is not Sophi. */
	if ( 'sophi.php' !== basename( $plugin ) ) {
		return;
	}

	$version_key     = 'sophi_version';
	$current_version = get_transient( $version_key );

	if ( false === $current_version ) {
		$current_version = get_option( $version_key, false );

		if ( false !== $current_version ) {
			set_transient( $version_key, $current_version );
		}
	}

	if ( SOPHI_WP_VERSION === $current_version ) {
		return;
	}

	if ( false === $current_version || version_compare( $current_version, SOPHI_WP_VERSION, '<' ) ) {

		/** Cleanup logic before setting the new version of the plugin. */
		delete_option( 'sophi_site_automation_access_token' );

		$sophi_settings = get_option( 'sophi_settings' );
		unset( $sophi_settings['client_id'] );
		unset( $sophi_settings['client_secret'] );

		update_option( 'sophi_settings', $sophi_settings );
		update_option( $version_key, SOPHI_WP_VERSION, true );
		set_transient( $version_key, SOPHI_WP_VERSION );
	}
}

add_action( 'plugin_loaded', 'sophi_remove_stale_data' );

/**
 * Sophi HTTPS notice.
 */
function sophi_https_notice() {
	$screen = get_current_screen();
	if ( 'plugins' !== $screen->id ) {
		return;
	}
	?>
		<div class="notice notice-error">
			<p><?php esc_html_e( 'Sophi requires HTTPS. Please install SSL to your site and try again.', 'sophi-wp' ); ?></p>
		</div>
	<?php
}


/**
 * Sophi setup notice.
 */
function sophi_setup_notice() {
	$screen = get_current_screen();
	if ( 'plugins' !== $screen->id ) {
		return;
	}
	?>
		<div class="notice notice-error">
				<p><?php echo wp_kses_post( sprintf( __( 'Please set up your Sophi.io account in Settings > <a href="%s">Sophi.io</a>', 'sophi-wp' ), admin_url( 'options-general.php?page=sophi' ) ) ); ?></p>
		</div>
	<?php

}

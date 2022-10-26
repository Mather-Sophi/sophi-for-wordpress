<?php
/**
 * Settings page.
 *
 * @package SophiWP
 */

namespace SophiWP\Settings;

use SophiWP\SiteAutomation\Auth;
use function SophiWP\Utils\get_domain;
use function SophiWP\Utils\is_configured;

const SETTINGS_GROUP = 'sophi_settings';

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'admin_menu', $n( 'settings_page' ) );
	add_action( 'admin_init', $n( 'fields_setup' ) );
	add_filter( 'plugin_action_links_' . plugin_basename( SOPHI_WP_PATH . '/sophi.php' ), $n( 'add_action_links' ) );
}

/**
 * Register a settings page.
 */
function settings_page() {
	add_options_page(
		__( 'Sophi.io Settings', 'sophi-wp' ),
		__( 'Sophi.io', 'sophi-wp' ),
		'manage_options',
		'sophi',
		__NAMESPACE__ . '\render_settings_page'
	);
}

/**
 * Render callback for the settings form.
 */
function render_settings_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sophi.io Settings', 'sophi-wp' ); ?></h1>
		<div class="sophi-settings">
			<form method="post" action="options.php">
				<?php
				settings_fields( SETTINGS_GROUP );
				do_settings_sections( SETTINGS_GROUP );
				submit_button();
				?>
			</form>
			<div class="brand">
				<a href="https://sophi.io" class="logo" title="<?php esc_attr_e( 'Sophi', 'sophi-wp' ); ?>">
					<img src="<?php echo esc_url( trailingslashit( SOPHI_WP_URL ) . 'dist/images/logo.png' ); ?>" alt="<?php esc_attr_e( 'Sophi logo', 'sophi-wp' ); ?>" />
				</a>
				<p>
					<strong>
						<?php echo esc_html__( 'Sophi for WordPress', 'sophi-wp' ) . ' ' . esc_html__( 'by', 'sophi-wp' ); ?> <a href="https://10up.com" title="<?php esc_attr_e( '10up', 'sophi-wp' ); ?>"><?php esc_html_e( '10up', 'sophi-wp' ); ?></a>
					</strong>
				</p>
				<nav>
					<a href="https://github.com/globeandmail/sophi-for-wordpress#frequently-asked-questions" target="_blank" title="<?php esc_attr_e( 'FAQs', 'sophi-wp' ); ?>">
						<?php esc_html_e( 'FAQs', 'sophi-wp' ); ?><span class="dashicons dashicons-external"></span>
					</a>
					<a href="https://github.com/globeandmail/sophi-for-wordpress/issues" target="_blank" title="<?php esc_attr_e( 'Support', 'sophi-wp' ); ?>">
						<?php esc_html_e( 'Support', 'sophi-wp' ); ?><span class="dashicons dashicons-external"></span>
					</a>
				</nav>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Register the settings and fields.
 */
function fields_setup() {
	// Register the main settings.
	register_setting(
		SETTINGS_GROUP,
		SETTINGS_GROUP,
		__NAMESPACE__ . '\sanitize_settings'
	);

	// Add settings section.
	add_settings_section(
		'environment',
		__( 'Environment settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'environment',
		__( 'Environment', 'sophi-wp' ),
		__NAMESPACE__ . '\render_select',
		SETTINGS_GROUP,
		'environment',
		[
			'label_for'  => 'environment',
			'default'    => get_default_settings( 'environment' ),
			'input_type' => 'select',
			'options'    => [
				'prod' => __( 'Production', 'sophi-wp' ),
				'stg'  => __( 'Staging', 'sophi-wp' ),
				'dev'  => __( 'Development', 'sophi-wp' ),
			],
		]
	);

	// Add settings section.
	add_settings_section(
		'collector_settings',
		__( 'Collector settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'collector_url',
		__( 'Collector URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'collector_settings',
		[
			'label_for' => 'collector_url',
			'description' => __( 'Please use URL without http(s) scheme.', 'sophi-wp' ),
		]
	);

	add_settings_field(
		'tracker_client_id',
		__( 'Tracker Client ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'collector_settings',
		[
			'label_for' => 'tracker_client_id',
		]
	);

	add_settings_field(
		'tracker_address',
		__( 'Tracker Address', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'collector_settings',
		[
			'label_for' => 'tracker_address',
			'default'   => get_default_settings( 'tracker_address' ),
		]
	);

	// Add settings section
	add_settings_section(
		'sophi_api',
		__( 'Site Automation settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'host',
		__( 'Host', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'host',
		]
	);

	add_settings_field(
		'tenant_id',
		__( 'Tenant ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'tenant_id',
		]
	);

	add_settings_field(
		'site_automation_url',
		__( 'Site Automation URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'site_automation_url',
		]
	);

	// Add Auth settings section.
	add_settings_section(
		'sophi_api_auth',
		__( 'Override settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'sophi_override_client_id',
		__( 'Client ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api_auth',
		[
			'label_for' => 'sophi_override_client_id',
		]
	);

	add_settings_field(
		'sophi_override_client_secret',
		__( 'Client Secret', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api_auth',
		[
			'label_for' => 'sophi_override_client_secret',
		]
	);

	add_settings_field(
		'sophi_override_url',
		__( 'Override API URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api_auth',
		[
			'label_for'   => 'sophi_override_url',
			'description' => __( 'For example, https://xyz.sophi.io/v1/, please add a slash (/) in the end.', 'sophi-wp' ),
		]
	);

	// Add Advanced settings section.
	add_settings_section(
		'sophi_advanced',
		__( 'Advanced settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'query_integration',
		__( 'Query Integration', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_advanced',
		[
			'label_for'   => 'query_integration',
			'input_type'  => 'checkbox',
			'description' => __( 'Replace WP Query result with curated data from Sophi.', 'sophi-wp' ),
		]
	);
}

/**
 * Retrieve default setting(s).
 *
 * @param string $key Setting to retrieve.
 */
function get_default_settings( $key = '' ) {

	$default_environment = 'prod';

	if ( function_exists( 'wp_get_environment_type' ) ) {

		$environment_type = wp_get_environment_type();

		switch ( $environment_type ) {
			case 'local':
			case 'development':
				$default_environment = 'dev';
				break;
			case 'staging':
				$default_environment = 'stg';
				break;
			default:
				$default_environment = 'prod';
		}
	}

	$default = [
		'environment'                  => $default_environment,
		'collector_url'                => 'collector.sophi.io',
		'tracker_client_id'            => get_domain(),
    'tracker_address'              => 'https://cdn.sophi.io/latest/sophi.min.js',
		'host'                         => '',
		'tenant_id'                    => '',
		'site_automation_url'          => '',
		'sophi_override_url'           => '',
		'sophi_override_client_id'     => '',
		'sophi_override_client_secret' => '',
		'query_integration'            => 1,
	];

	if ( ! $key ) {
		return $default;
	}

	if ( isset( $default[ $key ] ) ) {
		return $default[ $key ];
	}

	return false;
}

/**
 * Sanitize and validate settings.
 *
 * @param array $settings Raw settings.
 */
function sanitize_settings( $settings ) {
	if ( empty( $settings['query_integration'] ) ) {
		$settings['query_integration'] = 0;
	}

	if ( empty( $settings['site_automation_url']) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Site Automation URL is required for Site Automation integration.', 'sophi-wp' )
		);
	} else if ( ! filter_var( $settings['site_automation_url'], FILTER_VALIDATE_URL ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Site Automation URL is invalid.', 'sophi-wp' )
		);
	}

	if ( ! empty( $settings['tracker_address'] ) && ! filter_var( $settings['tracker_address'], FILTER_VALIDATE_URL ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			sprintf( __( 'Tracker Address URL is invalid: %s', 'sophi-wp' ), $settings['tracker_address'] )
		);
		unset( $settings['tracker_address'] );
	}

	if ( empty( $settings['sophi_override_url']) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Sophi Override URL is required for Override actions.', 'sophi-wp' )
		);
	} else if ( ! filter_var( $settings['sophi_override_url'], FILTER_VALIDATE_URL ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Sophi Override URL is invalid.', 'sophi-wp' )
		);
	}

	if ( empty( $settings['collector_url']) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Collector URL can not be empty.', 'sophi-wp' )
		);
	} else {
		$url = str_replace( 'http://', '', $settings['collector_url'] );
		$url = str_replace( 'https://', '', $url );

		$settings['collector_url'] = $url;
	}

	if ( empty( $settings['host'] ) || empty( $settings['tenant_id'] ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Both Host and Tenant ID are required for Site Automation integration.', 'sophi-wp' )
		);
	}

	if ( empty( $settings['sophi_override_client_id'] ) || empty( $settings['sophi_override_client_secret'] ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Both Client ID and Client Secret are required to generate a token for API.', 'sophi-wp' )
		);
	}

	if ( isset( $settings['client_id'] ) ) {
		unset( $settings['client_id'] );
	}

	if ( isset( $settings['client_secret'] ) ) {
		unset( $settings['client_secret'] );
	}

	return $settings;
}

/**
 * Helper to get the settings.
 *
 * @param string $key Setting key. Optional.
 */
function get_sophi_settings( $key = '' ) {
	$defaults = get_default_settings();
	$settings = get_option( SETTINGS_GROUP, [] );
	$settings = wp_parse_args( $settings, $defaults );

	if ( $key && isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return $settings;
}

/**
 * Helper to render a input field
 *
 * @param array $args Arguments to render input.
 */
function render_input( $args ) {
	$setting_index = get_sophi_settings();
	$type          = $args['input_type'] ?? 'text';
	$value         = $setting_index[ $args['label_for'] ] ?? '';

	// Check for a default value
	$value = ( empty( $value ) && isset( $args['default'] ) ) ? $args['default'] : $value;
	$attrs = '';
	$class = '';

	switch ( $type ) {
		case 'text':
		case 'password':
			$attrs = ' value="' . esc_attr( $value ) . '"';
			$class = 'regular-text';
			break;
		case 'number':
			$attrs = ' value="' . esc_attr( $value ) . '"';
			$class = 'small-text';
			break;
		case 'checkbox':
			$attrs = ' value="1"' . checked( '1', $value, false );
			break;
	}
	?>
	<input
		type="<?php echo esc_attr( $type ); ?>"
		id="sophi-settings-<?php echo esc_attr( $args['label_for'] ); ?>"
		class="<?php echo esc_attr( $class ); ?>"
		name="<?php echo esc_attr( SETTINGS_GROUP ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
		<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
	<?php
	if ( ! empty( $args['description'] ) ) {
		if ( 'checkbox' === $type ) {
			echo '<span class="description">' . wp_kses_post( $args['description'] ) . '</span>';
		} else {
			echo '<br /><span class="description">' . wp_kses_post( $args['description'] ) . '</span>';
		}
	}
}

/**
 * Helper to render a input field
 *
 * @param array $args Arguments to render select.
 */
function render_select( $args ) {
	$setting_index = get_sophi_settings();
	$value         = $setting_index[ $args['label_for'] ] ?? '';
	$options       = $args['options'] ?? [];

	// Check for a default value
	$value = ( empty( $value ) && isset( $args['default'] ) ) ? $args['default'] : $value;
	?>
	<select
		id="sophi-settings-<?php echo esc_attr( $args['label_for'] ); ?>"
		name="<?php echo esc_attr( SETTINGS_GROUP ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
	>
		<?php
		foreach ( $options as $option_value => $label ) {
			printf(
				'<option value="%1$s" %3$s>%2$s</option>',
				$option_value,
				esc_html( $label ),
				$option_value === $value ? 'selected="selected"' : ''
			);
		}
		?>
	</select>
	<?php
	if ( ! empty( $args['description'] ) ) {
		echo '<br /><span class="description">' . wp_kses_post( $args['description'] ) . '</span>';
	}
}

/**
 * Add setting page to plugin action links.
 *
 * @param array $actions Plugin actions.
 *
 * @return array
 */
function add_action_links ( $actions ) {
	if ( ! is_configured() ) {
		$action_label = __('Set up your Sophi.io account', 'sophi-wp');
	} else {
		$action_label = __('Settings', 'sophi-wp');
	}
	return array_merge(
		[
			'<a href="' . admin_url('options-general.php?page=sophi') . '">' . $action_label . '</a>',
		],
		$actions
	);
}

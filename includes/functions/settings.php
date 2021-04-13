<?php
/**
 * Settings page.
 *
 * @package SophiWP
 */

namespace SophiWP\Settings;

use SophiWP\Curator\Auth;
use function SophiWP\Utils\get_domain;

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
	add_filter( 'plugin_action_links_' . plugin_basename( SOPHI_WP_PATH . '/sophi-for-wordpress.php' ), $n( 'add_action_links' ) );
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

	// Add settings section
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

	// Add settings section
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

	// Add settings section
	add_settings_section(
		'sophi_api',
		__( 'Curator settings', 'sophi-wp' ),
		'',
		SETTINGS_GROUP
	);

	add_settings_field(
		'sophi_client_id',
		__( 'Client ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'sophi_client_id',
		]
	);

	add_settings_field(
		'sophi_client_secret',
		__( 'Client Secret', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'sophi_client_secret',
		]
	);

	add_settings_field(
		'sophi_curator_url',
		__( 'Curator URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
		[
			'label_for' => 'sophi_curator_url',
		]
	);

	add_settings_field(
		'query_integration',
		__( 'Query Integration', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		SETTINGS_GROUP,
		'sophi_api',
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
	$default = [
		'environment'         => 'prod',
		'collector_url'       => 'https://collector.sophi.io',
		'tracker_client_id'   => get_domain(),
		'sophi_client_id'     => '',
		'sophi_client_secret' => '',
		'sophi_curator_url'   => '',
		'query_integration'   => 1,
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

	if ( ! empty( $settings['sophi_client_id'] && ! empty( $settings['sophi_client_secret'] ) ) ) {
		$auth = new Auth();
		$response = $auth->request_access_token( $settings['sophi_client_id'], $settings['sophi_client_secret'] );
		if ( is_wp_error( $response ) ) {
			add_settings_error(
				SETTINGS_GROUP,
				SETTINGS_GROUP,
				$response->get_error_message()
			);
		}
	} else {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Both client ID and client secret are required for Curator integration!', 'sophi-wp' )
		);
	}

	if ( empty( $settings['sophi_curator_url']) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Client URL is required for Curator integration!', 'sophi-wp' )
		);
	} else if ( ! filter_var( $settings['sophi_curator_url'], FILTER_VALIDATE_URL ) ) {
		add_settings_error(
			SETTINGS_GROUP,
			SETTINGS_GROUP,
			__( 'Sophi Curator URL is invalid!', 'sophi-wp' )
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
				esc_attr( $option_value ),
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
	return array_merge(
		[
			'<a href="' . admin_url('options-general.php?page=sophi') . '">' . __('Set up your Sophi.io account', 'sophi-wp') . '</a>',
		],
		$actions
	);
}

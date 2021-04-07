<?php
/**
 * Settings page.
 *
 * @package SophiWP
 */

namespace SophiWP\Settings;

use function SophiWP\Utils\get_domain;

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
	add_action( 'admin_menu', $n( 'fields_setup' ) );
}

/**
 * Register a settings page.
 */
function settings_page() {
	add_options_page(
		__( 'Sophi Settings', 'sophi-wp' ),
		__( 'Sophi Settings', 'sophi-wp' ),
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
	<h1>Sophi Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'sophi' );
			do_settings_sections( 'sophi' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/**
 * Register the settings and fields.
 */
function fields_setup() {
	// Register the main settings.
	register_setting(
		'sophi',
		'sophi',
		[
			'type'              => 'array',
			'sanitize_callback' => __NAMESPACE__ . '\sanitize_settings',
			'default'           => [],
		]
	);

	// Add settings section
	add_settings_section(
		'environment',
		__( 'Environment settings', 'sophi-wp' ),
		'',
		'sophi'
	);

	add_settings_field(
		'environment',
		__( 'Environment', 'sophi-wp' ),
		__NAMESPACE__ . '\render_select',
		'sophi',
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
		'sophi'
	);

	add_settings_field(
		'collector_url',
		__( 'Collector URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
		'collector_settings',
		[
			'label_for' => 'collector_url',
		]
	);

	add_settings_field(
		'tracker_client_id',
		__( 'Tracker Client ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
		'collector_settings',
		[
			'label_for' => 'tracker_client_id',
		]
	);

	// Add settings section
	add_settings_section(
		'sophi_api',
		__( 'Sophi API settings', 'sophi-wp' ),
		'',
		'sophi'
	);

	add_settings_field(
		'sophi_client_id',
		__( 'Sophi Client ID', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
		'sophi_api',
		[
			'label_for' => 'sophi_client_id',
		]
	);

	add_settings_field(
		'sophi_client_secret',
		__( 'Sophi Client Secret', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
		'sophi_api',
		[
			'label_for' => 'sophi_client_secret',
		]
	);

	add_settings_field(
		'sophi_curator_url',
		__( 'Sophi Curator URL', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
		'sophi_api',
		[
			'label_for' => 'sophi_curator_url',
		]
	);

	add_settings_field(
		'query_integration',
		__( 'Query Integration', 'sophi-wp' ),
		__NAMESPACE__ . '\render_input',
		'sophi',
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
		'sophi_client_id'     => '',
		'sophi_client_secret' => '',
		'sophi_curator_url'   => '',
		'tracker_client_id'   => get_domain(),
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
	return $settings;
}

/**
 * Helper to get the settings.
 *
 * @param string $key Setting key. Optional.
 */
function get_sophi_settings( $key = '' ) {
	$defaults = get_default_settings();
	$settings = get_option( 'sophi', [] );
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
		name="sophi[<?php echo esc_attr( $args['label_for'] ); ?>]"
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
		name="sophi[<?php echo esc_attr( $args['label_for'] ); ?>]"
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

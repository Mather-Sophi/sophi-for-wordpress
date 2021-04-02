<?php
/**
 * JS tracking.
 *
 * @package SophiWP
 */

namespace SophiWP\Tracking;

use function SophiWP\Settings\get_sophi_settings;
use function SophiWP\Utils\get_domain;
use function SophiWP\Utils\get_section_name;
use function SophiWP\Utils\get_breadcrumb;
use function SophiWP\Core\script_url;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'wp_enqueue_scripts', $n( 'enqueue_scripts' ) );
	add_filter( 'amp_analytics_entries', $n( 'amp_tracking' ) );
}

/**
 * Enqueue tracking scripts.
 */
function enqueue_scripts() {
	if ( ! page_need_tracking() ) {
		return;
	}

	wp_enqueue_script(
		'sophi-tag',
		script_url( 'sophi-tag', 'frontend' ),
		[],
		SOPHI_WP_VERSION,
		true
	);

	wp_localize_script(
		'sophi-tag',
		'SOPHIDATA',
		get_tracking_data()
	);
}

/**
 * Add tracking to AMP pages.
 *
 * @param array $analytics_entries An associative array of the analytics entries we want to output.
 *
 * return array
 */
function amp_tracking( $analytics_entries ) {
	if ( page_need_tracking() ) {
		$analytics_entries[] = [
			'type'   => 'snowplow_v2',
			'config' => wp_json_encode( get_amp_tracking_data() ),
		];
	}

	return $analytics_entries;
}

/**
 * Prepare data for JS tracking.
 */
function get_tracking_data() {
	global $wp, $post;

	$env = get_sophi_settings( 'environment' );

	$data = [
		'data'     => [
			'environment' => [
				'environment' => $env,
				'version'     => get_bloginfo( 'version' ),
			],
			'page'        => [
				'type'       => is_singular() ? 'article' : 'section',
				'breadcrumb' => get_breadcrumb(),
			],
			'content'     => [
				'type' => 'article',
			],
		],
		'settings' => [
			'client'            => get_domain(),
			'appId'             => get_sophi_settings( 'website_app_id' ),
			'collectorEndpoint' => get_sophi_settings( 'collector_url' ),
			'linkedDomains'     => [ get_domain() ],
			'plugin'            => [
				'adblock' => false,
				'private' => false,
				'video'   => true,
			],
		],

	];

	$section = get_section_name();

	if ( $section ) {
		$data['data']['page']['sectionName'] = $section;
	}

	if ( is_singular() ) {
		$data['data']['content']['contentId'] = strval( $post->ID );
	}

	if ( is_front_page() ) {
		$data['data']['page']['breadcrumb']  = 'homepage';
		$data['data']['page']['sectionName'] = 'homepage';
		$data['data']['page']['type']        = 'section';
	}

	if ( 'prod' === $env ) {
		$data['settings']['productionEndpoint'] = get_sophi_settings( 'collector_url' );
	}

	if ( 'stg' === $env ) {
		$data['settings']['stagingEndpoint'] = get_sophi_settings( 'collector_url' );
	}

	return apply_filters( 'sophi_tracking_data', $data );
}

/**
 * Prepare data for AMP tracking.
 */
function get_amp_tracking_data() {
	$data = [
		'vars'     => [
			'collectorHost'  => 'collector.sophi.io',
			'appId'          => sprintf( '%s-amp', get_domain() ),
			'customContexts' => get_custom_contexts(),
		],
		'linkers'  => [
			'enabled'            => true,
			'proxyOnly'          => false,
			'destinationDomains' => get_domain(),
		],
		'triggers' => [
			'defaultPageview'    => [
				'on'      => 'visible',
				'request' => 'pageView',
			],
			'trackFirstPagePing' => [
				'on'        => 'timer',
				'request'   => 'pagePing',
				'timerSpec' => [
					'interval'       => 5,
					'maxTimerLength' => 4.99,
					'immediate'      => false,
					'startSpec'      => [
						'on'       => 'visible',
						'selector' => ':root',
					],
					'stopSpec'       => [
						'on'       => 'hidden',
						'selector' => ':root',
					],
				],
			],
			'trackPagePings'     => [
				'on'        => 'timer',
				'request'   => 'pagePing',
				'timerSpec' => [
					'interval'       => 20,
					'maxTimerLength' => 1800,
					'immediate'      => false,
					'startSpec'      => [
						'on'       => 'visible',
						'selector' => ':root',
					],
					'stopSpec'       => [
						'on'       => 'hidden',
						'selector' => ':root',
					],
				],
			],
		],
	];

	return apply_filters( 'sophi_amp_tracking_data', $data );
}

/**
 * Get custom context for AMP tracking.
 */
function get_custom_contexts() {
	global $post;

	$page_data    = [
		'type'       => is_singular() ? 'article' : 'section',
		'breadcrumb' => get_breadcrumb(),
		'sectionName' => get_section_name(),
	];

	if ( is_singular() ) {
		$content_data = [
			'type' => 'article',
			'contentId' => strval( $post->ID ),
		];

		$context = sprintf(
			'%s,%s,%s',
			wp_json_encode(
				[
					'schema' => '__environment_schama_url__',
					'data'   => [
						'client'      => get_domain(),
						'environment' => get_sophi_settings( 'environment' ),
					],
				]
			),
			wp_json_encode(
				[
					'schema' => '__page_schama_url__',
					'data'   => $page_data,
				]
			),
			wp_json_encode(
				[
					'schema' => '__content_schama_url__',
					'data'   => $content_data,
				]
			)
		);
	} else {
		$context = sprintf(
			'%s,%s',
			wp_json_encode(
				[
					'schema' => '__environment_schama_url__',
					'data'   => [
						'client'      => get_domain(),
						'environment' => get_sophi_settings( 'environment' ),
					],
				]
			),
			wp_json_encode(
				[
					'schema' => '__page_schama_url__',
					'data'   => $page_data,
				]
			),
		);
	}


	// We need this indirect way to prevent the url encode two times.
	return str_replace(
		[ '__environment_schama_url__', '__page_schama_url__', '__content_schama_url__' ],
		[ 'iglu:com.globeandmail/environment/jsonschema/1-0-9', 'iglu:com.globeandmail/page/jsonschema/1-0-10', 'iglu:com.globeandmail/content/jsonschema/1-0-12' ],
		$context
	);
}

/**
 * Check if current page needs tracking.
 *
 * @return bool
 */
function page_need_tracking() {
	return apply_filters(
		'sophi_page_need_tracking',
		is_tax() || is_tag() || is_category() || is_singular() || is_front_page() || is_home()
	);
}

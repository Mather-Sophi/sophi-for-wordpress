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
use function SophiWP\Utils\get_post_content_type;
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
	global $post;

	$env = get_sophi_settings( 'environment' );

	$data = [
		'data'     => [
			'environment' => [
				'environment' => $env,
				'version'     => get_bloginfo( 'version' ),
			],
			'page'        => [
				'type'       => is_singular() ? get_post_content_type( $post ) : 'section',
				'breadcrumb' => get_breadcrumb(),
			],
			'content'     => [
				'type' => get_post_content_type( $post ),
			],
		],
		'settings' => [
			'client'            => get_sophi_settings( 'tracker_client_id' ),
			'appId'             => sprintf( '%s:website', get_sophi_settings( 'tracker_client_id' ) ),
			'collectorEndpoint' => get_sophi_settings( 'collector_url' ),
			'linkedDomains'     => [ get_domain() ],
			'noConfigFile'      => true,
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

	/**
	 * Filter JS tracking data sent to Sophi Collector that gets generated on normal (non-AMP) pageviews.  If you have a unique need and the default data doesn't match those needs, then you can utilize this filter to modify that data as needed.
	 *
	 * @since 1.0.0
	 * @hook sophi_tracking_data
	 *
	 * @param {array} $data JS tracking data.
	 *
	 * @return {array} JS tracking data.
	 */
	return apply_filters( 'sophi_tracking_data', $data );
}

/**
 * Prepare data for AMP tracking.
 */
function get_amp_tracking_data() {
	$data = [
		'vars'     => [
			'collectorHost'  => 'collector.sophi.io',
			'appId'          => sprintf( '%s:amp', get_sophi_settings( 'tracker_client_id' ) ),
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

	/**
	 * Filter AMP tracking data sent to Sophi Collector that gets generated on AMP pageviews.  If you have a unique need and the default data doesn't match those needs, then you can utilize this filter to modify that data as needed.
	 *
	 * @since 1.0.0
	 * @hook sophi_amp_tracking_data
	 *
	 * @param {array} $data AMP tracking data.
	 *
	 * @return {array} AMP tracking data.
	 */
	return apply_filters( 'sophi_amp_tracking_data', $data );
}

/**
 * Get custom context for AMP tracking.
 */
function get_custom_contexts() {
	global $post;

	$page_data    = [
		'type'       => is_singular() ? get_post_content_type( $post ) : 'section',
		'breadcrumb' => get_breadcrumb(),
		'sectionName' => get_section_name(),
	];

	if ( is_singular() ) {
		$content_data = [
			'type' => get_post_content_type( $post ),
			'contentId' => strval( $post->ID ),
		];

		$context = sprintf(
			'%s,%s,%s',
			wp_json_encode(
				[
					'schema' => '__environment_schama_url__',
					'data'   => [
						'client'      => get_sophi_settings( 'tracker_client_id' ),
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
						'client'      => get_sophi_settings( 'tracker_client_id' ),
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
 * Check if current page needs JS tracking.  By default this will show on most every page (things like the 404 page will be excluded and if a site has a search page it will be excluded as well).  If you have certain pages that you don't want tracked or pages that need to be tracked that aren't part of the default then this filter can be used to modify the default behavior.
 *
 * @return bool
 */
function page_need_tracking() {
	/**
	 * Filter the type of page that needs tracking.
	 *
	 * @since 1.0.0
	 * @hook sophi_page_need_tracking
	 *
	 * @param {bool} $need_tracking Whether tracking should be enabled.
	 *
	 * @return {boold} Whether tracking should be enabled.
	 */
	return apply_filters(
		'sophi_page_need_tracking',
		is_tax() || is_tag() || is_category() || is_singular() || is_front_page() || is_home()
	);
}

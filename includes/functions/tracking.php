<?php
/**
 * JS tracking.
 *
 * @package SophiWP
 */

namespace SophiWP\Tracking;

use function SophiWP\Settings\get_sophi_settings;

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
	if ( ! is_archive() && ! is_singular() ) {
		return;
	}

	wp_enqueue_script( 'sophi-tag', SOPHI_WP_URL . '/dist/js/sophi-tag.js', [], SOPHI_WP_VERSION, true );
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
	if ( is_archive() || is_singular() ) {
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

	$data = [
		'data'     => [
			'environment' => [
				'environment' => get_sophi_settings( 'environment' ),
				'version'     => get_bloginfo( 'version' ),
			],
			'page'        => [
				'type'       => 'article',
				'breadcrumb' => is_singular() ? get_breadcrumbs( $post ) : $wp->request,
			],
			'content'     => [
				'type' => 'article',
			],
		],
		'settings' => [
			'client'        => get_domain(),
			'appId'         => get_domain(),
			'linkedDomains' => [ get_domain() ],
			'plugin'        => [
				'adblock' => false,
				'private' => false,
				'video'   => false,
			],
		],

	];

	$section = is_singular() ? get_section_name( get_breadcrumbs( $post ) ) : get_section_name( $wp->request );

	if ( $section ) {
		$data['data']['page']['sectionName'] = $section;
	}

	if ( is_singular() ) {
		$data['data']['content']['contentId'] = $post->ID;
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
						'selector' => 'root',
					],
					'stopSpec'       => [
						'on'       => 'hidden',
						'selector' => 'root',
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
						'selector' => 'root',
					],
					'stopSpec'       => [
						'on'       => 'hidden',
						'selector' => 'root',
					],
				],
			],
		],
	];

	return apply_filters( 'sophi_amp_tracking_data', $data );
}

/**
 * Get breadcrumbs from the post URL.
 *
 * @param WP_Post $post Post object.
 *
 * @return string Breadcrumbs.
 */
function get_breadcrumbs( $post ) {
	$permalink = get_permalink( $post );
	$permalink = wp_parse_url( $permalink );
	return $permalink['path'];
}

/**
 * Get section name from the post URL.
 * For example, example.com/news/politics, news would be the section name.
 * Not all content will have a section name.
 *
 * @param string $path URL path.
 *
 * @return string Section name.
 */
function get_section_name( $path = '' ) {
	$parts = explode( '/', $path );
	$parts = array_filter( $parts );

	if ( 2 !== count( $parts ) ) {
		return '';
	}

	$section = array_slice( $parts, -2, 1 );

	return reset( $section );
}

/**
 * Get site domain.
 *
 * return string
 */
function get_domain() {
	$urlparts = wp_parse_url( home_url() );
	return $urlparts['host'];
}

/**
 * Get custom context for AMP tracking.
 */
function get_custom_contexts() {
	global $wp, $post;

	$section      = is_singular() ? get_section_name( get_breadcrumbs( $post ) ) : get_section_name( $wp->request );
	$page_data    = [
		'type'       => 'article',
		'breadcrumb' => is_singular() ? get_breadcrumbs( $post ) : $wp->request,
	];
	$content_data = [ 'type' => 'article' ];

	if ( $section ) {
		$page_data['sectionName'] = $section;
	}

	if ( is_singular() ) {
		$content_data['contentId'] = $post->ID;
	}

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

		// We need this indirect way to prevent the url encode two times.
		return str_replace(
			[ '__environment_schama_url__', '__page_schama_url__', '__content_schama_url__' ],
			[ 'iglu:com.globeandmail/environment/jsonschema/1-0-9', 'iglu:com.globeandmail/page/jsonschema/1-0-10', 'iglu:com.globeandmail/content/jsonschema/1-0-12' ],
			$context
		);
}

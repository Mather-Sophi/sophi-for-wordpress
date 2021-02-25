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
}

/**
 * Enqueue tracking scripts.
 */
function enqueue_scripts() {
	if ( ! is_archive() && ! is_singular() ) {
		return;
	}

	wp_enqueue_script( 'sophi-tag', SOPHI_WP_URL . '/dist/js/sophi-tag.js', [], SOPHI_WP_VERSION );
	wp_localize_script(
		'sophi-tag',
		'SOPHIDATA',
		get_sophi_tracking_data()
	);
}

/**
 * Prepare data for JS tracking.
 */
function get_sophi_tracking_data() {
	global $wp, $post;

	$urlparts = parse_url( home_url() );
	$domain   = $urlparts['host'];

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
			'client'        => $domain,
			'appId'         => $domain,
			'linkedDomains' => [ $domain ],
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

	if ( is_singular() ) {
		return apply_filters( 'sophi_post_tracking_data', $data );
	}
	return apply_filters( 'sophi_archive_tracking_data', $data );
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
	$permalink = parse_url( $permalink );
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

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

	if ( is_archive() || is_singular() ) {
		add_action( 'wp_enqueue_scripts', $n( 'enqueue_scripts' ) );
	}
}

/**
 * Enqueue tracking scripts.
 */
function enqueue_scripts() {
}

/**
 * Prepare data for JS tracking on single page.
 */
function get_single_post_tracking_data() {
	$post = get_post();
	return apply_filters(
		'sophi_post_tracking_data',
		[
			'pageType' => 'article',
			'breadcrumbs' => get_breadcrumbs( $post ),
			'sectionName' => get_section_name( get_breadcrumbs( $post ) ),
			'environment' => get_sophi_settings( 'environment' ),
			'environmentVersion' => get_bloginfo( 'version' ),
			'contentType' => 'article',
			'contentId' => $post->ID,
		]
	);
}

/**
 * Prepare data for JS tracking on archive page.
 */
function get_archive_tracking_data() {
	global $wp;
	return apply_filters(
		'sophi_archive_tracking_data',
		[
			'pageType' => 'section',
			'breadcrumbs' => $wp->request,
			'sectionName' => get_section_name( $wp->request ),
			'environment' => get_sophi_settings( 'environment' ),
			'environmentVersion' => get_bloginfo( 'version' ),
			'contentType' => 'article',
		]
	);
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

	if ( 1 === count( $parts ) ) {
		return '';
	}

	return array_slice( $parts, -2, 1 );
}

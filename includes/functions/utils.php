<?php
/**
 * Utility functions.
 *
 * @package SophiWP
 */

namespace SophiWP\Utils;

/**
 * Get breadcrumbs from the post URL.
 *
 * @return string Breadcrumbs.
 */
function get_breadcrumb() {
	global $wp;

	if ( is_singular() ) {
		$permalink = get_permalink();
		$permalink = wp_parse_url( $permalink );
		$parts     = explode( '/', $permalink['path'] );
		$parts     = array_filter( $parts );
		array_pop( $parts );
		return implode( ':', $parts );
	}

	$path = $wp->request;
	$path = untrailingslashit( $path );
	$path = rtrim( $path, '/\\' );
	return str_replace( '/', ':', $path );
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
	if ( ! $path ) {
		$path = get_breadcrumb();
	}

	$path  = str_replace( '/', ':', $path );
	$parts = explode( ':', $path );
	$parts = array_filter( $parts );

	if ( 2 !== count( $parts ) ) {
		return '';
	}

	$section = array_slice( $parts, -2, 1 );

	return reset( $section );
}

/**
 * Get post tags name for post.
 *
 * @param WP_Post $post Post object.
 *
 * @return array Array of tag name.
 */
function get_post_tags( $post ) {
	$tags = get_the_tags( $post );
	if ( ! is_array( $tags ) ) {
		return [];
	}

	return array_map(
		function( $tag ) {
			return $tag->name;
		},
		$tags
	);
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

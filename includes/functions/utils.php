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

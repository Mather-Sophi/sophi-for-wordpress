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

	if ( is_front_page() ) {
		return 'homepage';
	}

	if ( is_singular() ) {
		$post = get_post();

		// If current post is hierarchical, we use page ancestors for breadcrumb.
		if ( is_post_type_hierarchical( $post->post_type ) ) {
			$ancestors = array_map(
				function( $parent ) {
					$post_object = get_post( $parent );
					return $post_object->post_name;
				},
				get_post_ancestors( $post )
			);

			if ( count( $ancestors ) > 0 ) {
				return implode(
					':',
					array_reverse( $ancestors )
				);
			}
		}

		// If the current post isn't hierarchical, we use taxonomy.
		$taxonomies = array_filter(
			get_object_taxonomies( $post ),
			'is_taxonomy_hierarchical'
		);

		if ( count( $taxonomies ) > 0 ) {
			$terms = get_the_terms( $post, $taxonomies[0] );

			if ( count( $terms ) > 0 ) {
				return get_term_breadcrumb( $terms[0] );
			}
		} else { // Just return the current term if it's not hierarchical.
			$non_hierarchial_taxonomies = array_filter(
				get_object_taxonomies( $post ),
				function( $taxonomy ) {
					return ! is_taxonomy_hierarchical( $taxonomy );
				}
			);
			if ( count( $non_hierarchial_taxonomies ) > 0 ) {
				$terms = get_the_terms( $post, $non_hierarchial_taxonomies[0] );

				if ( count( $terms ) > 0 ) {
					return $terms[0]->slug;
				}
			}
		}

		// Use post type archive as the fallback.
		$post_type_obj = get_post_type_object( $post->post_type );

		if ( ! $post_type_obj->has_archive ) {
			return '';
		}

		if ( get_option( 'permalink_structure' ) && is_array( $post_type_obj->rewrite ) ) {
			return $post_type_obj->rewrite['slug'];
		}

		return $post_type_obj->post_type;
	}

	if ( is_tax() || is_tag() || is_category() ) {
		$current_term = get_queried_object();
		return get_term_breadcrumb( $current_term );
	}

	return '';
}

/**
 * Get breadcrumb string for a single term.
 *
 * @param WP_Term $term Term object.
 *
 * @return string
 */
function get_term_breadcrumb( $term ) {
	$term_ancestors = array_map(
		function( $item ) use ( $term ) {
			$term_object = get_term( $item, $term->taxonomy );
			return $term_object->slug;
		},
		get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' )
	);

	$terms = array_merge( [ $term->slug ], $term_ancestors );

	return implode(
		':',
		array_reverse( $terms )
	);
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

	if ( 0 === count( $parts ) ) {
		return '';
	}

	return $parts[0];
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

/**
 * Get supported post types.
 *
 * @return array
 */
function get_supported_post_types() {
	/**
	 * Filter supported post types, the plugin supports Posts and Pages by default.  If you have Custom Post Types that should be sent to Collector, then this filter will need to be used to add those.
	 *
	 * @since 1.0.0
	 *
	 * @hook sophi_supported_post_types
	 *
	 * @param {array} $post_types Supported post types.
	 *
	 * @return {array} Supported post types.
	 */
	return apply_filters( 'sophi_supported_post_types', [ 'post', 'page' ] );
}

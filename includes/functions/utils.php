<?php
/**
 * Utility functions.
 *
 * @package SophiWP
 */

namespace SophiWP\Utils;

use function SophiWP\Settings\get_sophi_settings;

/**
 * Get breadcrumbs from the post URL.
 *
 * @return string Breadcrumbs.
 */
function get_breadcrumb() {
	if ( is_front_page() ) {
		return 'homepage';
	}

	if ( is_singular() ) {
		$post = get_post();
		return get_post_breadcrumb( $post );
	}

	if ( is_tax() || is_tag() || is_category() ) {
		$current_term = get_queried_object();
		return get_term_breadcrumb( $current_term );
	}

	return '';
}

/**
 * Get post breadcrumb.
 *
 * @param WP_Post $post Post object.
 *
 * @return string
 */
function get_post_breadcrumb( $post ) {
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
		function( $taxonomy ) {
			$taxonomy_object = get_taxonomy( $taxonomy );
			return $taxonomy_object->hierarchical && $taxonomy_object->public && $taxonomy_object->publicly_queryable;
		}
	);

	if ( count( $taxonomies ) > 0 ) {
		/**
		 * Filter the taxonomy to use to create breadcrumb. Default to the first public and hierarchial taxonomy
		 * attached to the post.
		 *
		 * @since 1.0.4
		 *
		 * @hook sophi_taxonomy_for_breadcrumb
		 *
		 * @param {string}  $taxonomy Taxonomy used for breadcrumb.
		 * @param {WP_Post} $post     Post object.
		 *
		 * @return {string} Taxonomy used for breadcrumb..
		 */
		$taxonomy = apply_filters( 'sophi_taxonomy_for_breadcrumb', $taxonomies[0], $post );
		$terms    = get_the_terms( $post, $taxonomy );

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
	$parts = get_section_names( $path );

	if ( 0 === count( $parts ) ) {
		return '';
	}

	return $parts[0];
}

/**
 * Get section names from the post URL.
 * For example, example.com/news/politics/article-slug, this function would return ['news', 'politics'].
 *
 * @param string $path URL path.
 *
 * @return array Section names.
 */
function get_section_names( $path = '' ) {
	if ( ! $path ) {
		$path = get_breadcrumb();
	}

	$path  = str_replace( '/', ':', $path );
	$parts = explode( ':', $path );
	$parts = array_filter( $parts );

	if ( 0 === count( $parts ) ) {
		return [];
	}

	return $parts;
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

/**
 * Check if Sophi is configured or not.
 *
 * @return bool
 */
function is_configured() {
	$settings = get_sophi_settings();

	unset( $settings['environment'] );
	unset( $settings['query_integration'] );

	$settings = array_filter(
		$settings,
		function( $item ) {
			return empty( $item );
		}
	);

	if ( count( $settings ) > 0 ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Get post data type.
 *
 * @since 1.0.4
 *
 * @param WP_Post $post Post object.
 *
 * @return string Post data type, can be article|video|audio|image
 */
function get_post_content_type( $post ) {

	/**
	 * Filter data type of the given post.
	 *
	 * @since 1.0.0
	 * @hook sophi_post_content_type
	 *
	 * @param {string}  $type Post data type, one of article|video|audio|image
	 * @param {WP_Post} $post WP_Post object.
	 *
	 * @return {string} Post data type.
	 */
	$type = apply_filters( 'sophi_post_content_type', get_post_format( $post ), $post );

	if ( ! in_array( $type, [ 'video', 'audio', 'image' ], true ) ) {
		$type = 'article';
	}

	return $type;
}

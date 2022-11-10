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
		 * Filter the hierarchial taxonomy to use to create breadcrumb. Default to the first
		 * public and hierarchial taxonomy attached to the post.
		 *
		 * @since 1.0.4
		 *
		 * @hook sophi_hierarchial_taxonomy_for_breadcrumb
		 *
		 * @param {string}  $taxonomy Taxonomy used for breadcrumb.
		 * @param {WP_Post} $post     Post object.
		 *
		 * @return {string} Taxonomy used for breadcrumb..
		 */
		$taxonomy = apply_filters( 'sophi_hierarchial_taxonomy_for_breadcrumb', $taxonomies[0], $post );
		$terms    = get_the_terms( $post, $taxonomy );

		if ( is_array( $terms ) && count( $terms ) > 0 ) {
			return get_term_breadcrumb( $terms[0] );
		}
	} else { // Just return the current term if it's not hierarchical.
		$non_hierarchial_taxonomies = array_filter(
			get_object_taxonomies( $post ),
			function( $taxonomy ) {
				$taxonomy_object = get_taxonomy( $taxonomy );
				return ! $taxonomy_object->hierarchical && $taxonomy_object->public && $taxonomy_object->publicly_queryable;
			}
		);

		if ( count( $non_hierarchial_taxonomies ) > 0 ) {
			/**
			 * Filter the non hierarchial taxonomy to use to create breadcrumb. Default to the first
			 * public and non hierarchial taxonomy attached to the post.
			 *
			 * @since 1.0.4
			 *
			 * @hook sophi_non_hierarchial_taxonomy_for_breadcrumb
			 *
			 * @param {string}  $taxonomy Taxonomy used for breadcrumb.
			 * @param {WP_Post} $post     Post object.
			 *
			 * @return {string} Taxonomy used for breadcrumb..
			 */
			$taxonomy = apply_filters( 'sophi_non_hierarchial_taxonomy_for_breadcrumb', $non_hierarchial_taxonomies[0], $post );
			$terms    = get_the_terms( $post, $taxonomy );

			if ( is_array( $terms ) && count( $terms ) > 0 ) {
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
 * @param string $section Settings section to check for. Default 'all'.
 * @return bool
 */
function is_configured( $section = 'all' ) {
	$settings = get_sophi_settings();

	switch ( $section ) {
		case 'collector':
			$settings = array_filter(
				$settings,
				function( $item, $key ) {
					return in_array( $key, [ 'collector_url', 'tracker_address', 'tracker_client_id' ], true ) && empty( $item );
				},
				ARRAY_FILTER_USE_BOTH
			);
			break;
		case 'automation':
			$settings = array_filter(
				$settings,
				function( $item, $key ) {
					return in_array( $key, [ 'host', 'tenant_id', 'site_automation_url' ], true ) && empty( $item );
				},
				ARRAY_FILTER_USE_BOTH
			);
			break;
		case 'override':
			$settings = array_filter(
				$settings,
				function( $item, $key ) {
					return in_array( $key, [ 'sophi_override_url', 'sophi_override_client_id', 'sophi_override_client_secret' ], true ) && empty( $item );
				},
				ARRAY_FILTER_USE_BOTH
			);
			break;
		case 'all':
		default:
			$settings = array_filter(
				$settings,
				function( $item, $key ) {
					return ! in_array( $key, [ 'environment', 'query_integration' ], true ) && empty( $item );
				},
				ARRAY_FILTER_USE_BOTH
			);
			break;
	}

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

/**
 * Get the number of embedded images in a post content.
 *
 * @since 1.1.0
 *
 * @param string $post_content Post content.
 *
 * @return int|false Number of embedded images or false on failure.
 */
function get_number_of_embedded_images( $post_content ) {
	$dom = new \DOMDocument();

	if ( ! empty( $post_content ) && @$dom->loadHTML( wp_kses( $post_content, array( 'img' => true ) ) ) ) {
		$images = $dom->getElementsByTagName('img');

		return $images->count();
	}

	return false;
}

/**
 * Build the category tree
 *
 * This function returns an array with the following structure
 *
 * [
 *  	[parent_term_id] => [
 * 			[children] => [
 * 				[child_term_id] => [
 * 					[name]     => 'child_term_name',
 * 					[children] => [
 * 						[...]
 * 					]
 * 				]
 * 			]
 * 		]
 * 		[...]
 * ]
 *
 * @param array   $categories_tree  Array used to hold the categories (passed by reference)
 * @param WP_Term $child_category   Child category
 * @param array   $ancestors        The ancestors ids of child category from highest to lowest in the hierarchy
 * @param integer $key              Position in the hierarchy of ancestors
 * @param integer $depth_level      Check how depth we are to avoid an infinite loop
 * @return void
 */
function build_category_tree( &$categories_tree, $child_category, $ancestors, $key = 0, $depth_level = 0 ) {
	$count = count( $ancestors );

	if ( $key < $count && 200 > $depth_level ) {
		// If the category is not on the category tree, add it.
		if ( ! array_key_exists( $ancestors[ $key ], $categories_tree ) ) {
			$categories_tree[ $ancestors[ $key ] ]['children'] = [];
		}

		// Fill with child category name
		if ( $ancestors[ $key ] === $child_category->parent ) {
			$categories_tree[ $ancestors[ $key ] ]['children'][ $child_category->term_id ]['name'] = $child_category->name;
		}

		// Go to the next level of hierarchy
		build_category_tree( $categories_tree[ $ancestors[ $key ] ]['children'], $child_category, $ancestors, $key + 1, $depth_level + 1 );
	}

}

/**
 * Return the categories in a flat array preserving the hierarchical order
 *
 * @param array $categories_tree The categories tree with the structure used by build_category_tree().
 * @return array
 */
function get_categories_hierarchical( $categories_tree ) {
	global $arr;

	foreach ( $categories_tree as $category ) {
		if ( ! empty( $category['name'] ) ) {
			$arr[] = $category['name'];
		}

		if ( ! empty( $category['children'] ) ) {
			get_categories_hierarchical( $category['children'] );
		}

	}

	return $arr;
}

/**
 * Return an array of category paths from a given post.
 *
 * @param int $post_id The post ID.
 * @return array The array of category paths, or an empty array if no categories.
 */
function get_post_categories_paths( $post_id ) {
	$categories    = get_the_category( $post_id );
	$paths         = [];

	foreach ( $categories as $category ) {
		if ( is_a( $category, 'WP_Term' ) && 'category' === $category->taxonomy ) {
			$hierarchical_slugs = [];
			$ancestors          = get_ancestors( $category->term_id, $category->taxonomy, 'taxonomy' );
			foreach ( (array) $ancestors as $ancestor ) {
				$ancestor_term        = get_term( $ancestor, $category->taxonomy );
				$hierarchical_slugs[] = $ancestor_term->slug;
			}
			$hierarchical_slugs   = array_reverse( $hierarchical_slugs );
			$hierarchical_slugs[] = $category->slug;
			$paths[] = '/' . implode( '/', $hierarchical_slugs );
		}
	}

	return $paths;
}

/**
 * Get the post categories preserving the hierarchical order
 *
 * @param int $post_id
 * @return array
 */
function get_post_categories( $post_id ) {

	$categories    = get_the_category( $post_id );
	$transient_key = 'sophi-post-' . $post_id . '-categories';

	if ( empty( $categories ) ) {
		delete_transient( $transient_key );

		return [];
	}

	/**
	 * Return an array with term_id, name and parent
	 *
	 * @param WP_Term $category
	 * @return array
	 */
	$map_termid_name_parent = function ( $category ) {
		return [
			'term_id' => $category->term_id,
			'name'    => $category->name,
			'parent'  => $category->parent,
		];
	};

	/**
	 * Order by term_id
	 *
	 * @param WP_Term $category_a
	 * @param WP_Term $category_b
	 * @return int
	 */
	$order_by_term_id = function ( $category_a, $category_b ) {
		$term_id_a = $category_a->term_id;
		$term_id_b = $category_b->term_id;

		if ( $term_id_a == $term_id_b ) {
			return 0;
		}

		return ( $term_id_a < $term_id_b ) ? -1 : 1;
	};

	$cached_categories = get_transient( $transient_key );

	// If categories don't change, we'll return a cached value.
	if (
		false !== $cached_categories &&
		is_array( $cached_categories ) &&
		! empty ( $cached_categories['formatted'] ) &&
		! empty( $cached_categories['term_id_name_parent_serialized'] )
	) {
		$categories_termid_name_parent   = array_map( $map_termid_name_parent, $categories );

		usort( $categories_termid_name_parent, $order_by_term_id );

		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$categories_termid_name_parent = serialize( $categories_termid_name_parent );

		if ( $categories_termid_name_parent === $cached_categories['term_id_name_parent_serialized'] ) {
			return $cached_categories['formatted'];
		}
	}

	$root_categories     = [];
	$children_categories = [];

	// Separete root and children categories
	foreach ( $categories as $category ) {
		if ( 0 === $category->parent ) {
			$root_categories[ $category->term_id ] = $category;
			$root_categories_id[]                  = $category->term_id;
		} else {
			$children_categories[] = $category;
		}
	}

	$categories_tree = [];
	// Build the category tree with all levels
	foreach ( $children_categories as $children_category ) {
		$ancestors = array_reverse( get_ancestors( $children_category->term_id, 'category', 'taxonomy' ) );

		build_category_tree( $categories_tree, $children_category, $ancestors );
	}

	// Fill the category tree with the information of root categories
	foreach ( $root_categories as $root_category ) {
		$categories_tree[ $root_category->term_id ]['name'] = $root_category->name;
	}

	$categories_formatted = get_categories_hierarchical( $categories_tree );

	$categories_termid_name_parent = array_map( $map_termid_name_parent, $categories );

	usort( $categories_termid_name_parent, $order_by_term_id );

	set_transient( $transient_key,
		[
			//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
			'term_id_name_parent_serialized' => serialize( $categories_termid_name_parent ),
			'formatted'                      => $categories_formatted
		]
	);

	return $categories_formatted;
}

/**
 * Utility function meant to be used to create appropriate data format for tracking events.
 *
 * @return string The formatted data.
 */
function get_wp_sophi_versions() {
	return 'wp-' . get_bloginfo( 'version' ) . ':plugin-' . SOPHI_WP_VERSION;
}

/**
 * Get the primary term name.
 *
 * @param string $taxonomy Optional. The taxonomy to get the primary term ID for. Defaults to category.
 * @param int    $post_id            Optional. Post to get the primary term ID for.
 *
 * @return string
 */
function get_primary_category( $post_id = 0, $taxonomy = 'category' ) {
	if ( ! function_exists( 'yoast_get_primary_term' ) || ! yoast_get_primary_term( $taxonomy, $post_id ) ) {
		$post_terms = wp_get_post_terms( $post_id, $taxonomy );

		if ( is_array( $post_terms ) && count( $post_terms ) > 0 ) {
			return $post_terms[0]->name;
		} else {
			return '';
		}
	}

	$primary_term = yoast_get_primary_term( $taxonomy, $post_id );

	return $primary_term;
}

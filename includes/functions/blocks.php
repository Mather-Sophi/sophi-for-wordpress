<?php
/**
 * Sophi Blocks setup
 *
 * @package SophiWP
 */

namespace SophiWP\Blocks;

use function SophiWP\Utils\get_supported_post_types;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_filter( 'block_categories', $n( 'blocks_categories' ), 10, 2 );

	register_blocks();
}

/**
 * Add in blocks that are registered in this theme
 *
 * @return void
 */
function register_blocks() {

	// Require custom blocks.
	require_once SOPHI_WP_INC . '/blocks/curator-block/register.php';

	// Call block register functions for each block.
	CuratorBlock\register();
}

/**
 * Filters the registered block categories.
 *
 * @param array  $categories Registered categories.
 * @param object $post       The post object.
 *
 * @return array Filtered categories.
 */
function blocks_categories( $categories, $post ) {
	if ( ! in_array( $post->post_type, get_supported_post_types(), true ) ) {
		return $categories;
	}

	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'sophi-blocks',
				'title' => __( 'Sophi Blocks', 'sophi-wp' ),
			),
		)
	);
}

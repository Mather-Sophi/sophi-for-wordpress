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

	add_filter( 'block_categories_all', $n( 'blocks_categories' ), 10, 2 );

	register_blocks();
}

/**
 * Add in blocks that are registered in this theme
 *
 * @return void
 */
function register_blocks() {

	// Require custom blocks.
	require_once SOPHI_WP_INC . '/blocks/site-automation-block/register.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	// Call block register functions for each block.
	SiteAutomationBlock\register();

	// Require custom blocks.
	require_once SOPHI_WP_INC . '/blocks/sophi-page-list-item/register.php'; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	// Call block register functions for each block.
	SiteAutomationItemBlock\register();
}

/**
 * Filters the registered block categories.
 *
 * @param array  $categories           array                   Registered categories.
 * @param object $block_editor_context WP_Block_Editor_Context The current block editor context.
 *
 * @return array Filtered categories.
 */
function blocks_categories( $categories, $block_editor_context ) {
	if ( isset( $block_editor_context->post ) && ! in_array( $block_editor_context->post->post_type, get_supported_post_types(), true ) ) {
		return $categories;
	}

	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'sophi-blocks',
				'title' => __( 'Sophi.io', 'sophi-wp' ),
			),
		)
	);
}

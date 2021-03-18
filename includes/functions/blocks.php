<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpScaffold\Core
 */

namespace SophiWP\Blocks;

/**
 * Set up blocks
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

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

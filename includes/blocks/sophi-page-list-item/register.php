<?php
/**
 * Site automation item block setup
 *
 * @package SophiWP
 */

namespace SophiWP\Blocks\SiteAutomationItemBlock;

/**
 * Register the block
 */
function register() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	// Register the block.
	register_block_type_from_metadata(
		SOPHI_WP_INC . '/blocks/sophi-page-list-item',
	);
}

<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpTheme\Blocks\Example
 */

namespace SophiWP\Blocks\CuratorBlock;

/**
 * Register the block
 */
function register() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	// Register the block.
	register_block_type_from_metadata(
		SOPHI_WP_INC . '/blocks/curator-block', // this is the directory where the block.json is found.
		[
			'render_callback' => $n( 'render_block_callback' ),
		]
	);
}

/**
 * Render callback method for the block
 *
 * @param array  $attributes The blocks attributes
 * @param string $content    Data returned from InnerBlocks.Content
 * @param array  $block      Block information such as context.
 *
 * @return string The rendered block markup.
 */
function render_block_callback( $attributes, $content, $block ) {
	if ( empty( $attributes['pageName'] ) || empty( $attributes['widgetName'] ) ) {
		return '';
	}

	$curated_posts = get_posts(
		[
			'sophi_curated_page'   => $attributes['pageName'],
			'sophi_curated_widget' => $attributes['widgetName'],
		]
	);

	if ( empty( $curated_posts ) ) {
		return '';
	}

	ob_start();
	include __DIR__ . '/markup.php';

	/**
	 * Filter the output of curator block.
	 *
	 * @since 1.0.0
	 * @hook sophi_curator_block_output
	 *
	 * @param {string} $output HTML output of curator block.
	 * @param {array} $curated_posts Array of curated posts. This is an array of WP_Post objects.
	 * @param {array} $attributes Block attributes.
	 * @param {array} $block Block context.
	 *
	 * @return {string} HTML output of curator block.
	 */
	return apply_filters(
		'sophi_curator_block_output',
		ob_get_clean(),
		$curated_posts,
		$attributes,
		$content,
		$block
	);
}

<?php
/**
 * Gutenberg Blocks setup
 *
 * @package TenUpTheme\Blocks\Example
 */

namespace SophiWP\Blocks\SiteAutomationBlock;

/**
 * Register the block
 */
function register() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};
	// Register the block.
	register_block_type_from_metadata(
		SOPHI_WP_INC . '/blocks/site-automation-block', // this is the directory where the block.json is found.
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

	$page_name   = sanitize_title( $attributes['pageName'] );
	$widget_name = sanitize_title( $attributes['widgetName'] );

	$curated_posts_transient_key = 'sophi_curated_posts_' . $page_name . '_' . $widget_name;

	if ( false === ( $curated_posts = get_transient( $curated_posts_transient_key ) ) ) {
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
		$curated_posts = get_posts(
			[
				'sophi_curated_page'   => $page_name,
				'sophi_curated_widget' => $widget_name,
			]
		);

		if ( empty( $curated_posts ) ) {
			return '';
		}

		set_transient( $curated_posts_transient_key, $curated_posts, 5 * MINUTE_IN_SECONDS );
	}

	ob_start();
	include __DIR__ . '/markup.php';

	/**
	 * Filter the output of the Site Automation block.
	 *
	 * @since 1.0.0
	 * @hook sophi_site_automation_block_output
	 *
	 * @param {string} $output HTML output of the Site Automation block.
	 * @param {array} $curated_posts Array of curated posts. This is an array of WP_Post objects.
	 * @param {array} $attributes Block attributes.
	 * @param {array} $block Block context.
	 *
	 * @return {string} HTML output of the Site Automation block.
	 */
	$output = apply_filters(
		'sophi_site_automation_block_output',
		ob_get_clean(),
		$curated_posts,
		$attributes,
		$content,
		$block
	);
	return sprintf(
		'<div class="sophi-site-automation-block" id="sophi-%1$s-%2$s" data-sophi-feature="%2$s">%3$s</div>',
		$page_name,
		$widget_name,
		$output
	);
}

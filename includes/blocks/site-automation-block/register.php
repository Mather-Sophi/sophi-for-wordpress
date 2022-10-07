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
 * Render callback method for the block. Also used by the rest endpoint '/curator-posts'.
 *
 * @param array  $attributes The blocks attributes
 * @param string $content    Data returned from InnerBlocks.Content
 * @param array  $block      Block information such as context.
 *
 * @return string|\WP_Post[] The rendered block markup OR WP_POst object to be returned to the REST callback.
 */
function render_block_callback( $attributes, $content, $block ) {
	$is_gb_editor = \defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && 'edit' === $_REQUEST['context'];

	// Render only on the front end.
	if( $is_gb_editor && 'via_rest' !== $content  ) {
		return '';
	}

	if ( empty( $attributes['pageName'] ) || empty( $attributes['widgetName'] ) ) {
		return '';
	}

	$page_name   = sanitize_title( $attributes['pageName'] );
	$widget_name = sanitize_title( $attributes['widgetName'] );

	/**
	 * Whether to bypass caching.
	 *
	 * @since 1.1.1
	 * @hook sophi_bypass_curated_posts_cache
	 *
	 * @param {bool} $bypass_cache True or false.
	 * @param {string} $page Page name.
	 * @param {string} $widget Widget name.
	 *
	 * @return {bool} Whether to bypass cache.
	 */
	$bypass_cache = apply_filters( 'sophi_bypass_curated_posts_cache', false, $page_name, $widget_name );

	$curated_posts = false;

	if ( ! $bypass_cache ) {
		$sophi_cached_response = new \WP_Query(
			[
				'post_name__in'          => [ "sophi-site-automation-data-{$page_name}-{$widget_name}" ],
				'post_type'              => 'sophi-response',
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_term_cache' => false
			]
		);

		if ( $sophi_cached_response->have_posts() ) {
			$last_update = get_post_meta( $sophi_cached_response->posts[0], 'sophi_site_automation_last_updated', true );

			if ( $last_update + 5 * MINUTE_IN_SECONDS > time() ) {
				$curated_posts = get_post_meta( $sophi_cached_response->posts[0], 'sophi_site_automation_data', true );
			}
		}
	}

	if ( $bypass_cache || ! $curated_posts ) {

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

	} else {
		$curated_posts = get_posts( [
			'post__in'  => $curated_posts,
			'post_type' => 'post',
			'orderby'   => 'post__in'
		] );
	}

	if ( 'via_rest' === $content ) {
		return $curated_posts;
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

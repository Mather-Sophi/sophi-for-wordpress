<?php
/**
 * Content sync
 *
 * @package SophiWP
 */

namespace SophiWP\ContentSync;

use function SophiWP\Settings\get_sophi_settings;

use Snowplow\Tracker\Tracker;
use Snowplow\Tracker\Subject;
use Snowplow\Tracker\Emitters\SyncEmitter;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'transition_post_status', $n( '\track_event' ), 10, 3 );
}

/**
 * Sending data to SnowPlow.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 */
function track_event( $new_status, $old_status, $post ) {
	$tracker = init_tracker();
	$action  = '';

	if ( ! $tracker ) {
		return false;
	}

	// publish, update, delete or unpublish

	if ( 'publish' === $new_status && 'publish' !== $old_status ) {
		$action = 'publish';
	} elseif ( 'publish' === $new_status && 'publish' === $old_status ) {
		$action = 'update';
	} elseif ( 'trash' === $new_status ) {
		$action = 'delete';
	} elseif ( 'publish' !== $new_status && 'publish' === $old_status ) {
		$action = 'unpublish';
	}

	if ( ! $action ) {
		return false;
	}

	$tracker->trackUnstructEvent(
		[
			'schema' => 'iglu:com.sophi/content_update/jsonschema/1-0-3',
			'data'   => get_post_data( $post, $action ),
		],
		[
			'schema' => 'iglu:com.globeandmail/environment/jsonschema/1-0-9',
			'data'   => [
				'environment' => get_sophi_settings( 'environment' ),
			],
		]
	);
}

/**
 * Initialize Snowplow tracker.
 *
 * @return Tracker
 */
function init_tracker() {
	$collector_url = get_sophi_settings( 'snowplow_api_url' );
	if ( ! $collector_url ) {
		return false;
	}

	$home_url = parse_url( home_url() );
	$app_id   = $home_url['host'] . '-cms';

	$emitter = new SyncEmitter( $collector_url, 'https', 'post', 1, false );
	$subject = new Subject();
	return new Tracker( $emitter, $subject, 'sophiTag', $app_id, false );
}

/**
 * Prepare post data to send to Snowplow.
 *
 * @param WP_Post $post Post object.
 * @param string  $action publish|update|delete|unpublish
 *
 * @return array
 */
function get_post_data( $post, $action ) {
	return apply_filters(
		'sophi_post_data',
		[
			'action'         => $action,
			'contentId'      => $post->ID,
			'headline'       => get_the_title( $post ),
			'byline'         => [ get_the_author_meta( 'display_name', $post->post_author ) ],
			'accessCategory' => 'free access',
			'datePublished'  => $post->post_date_gmt,
			'plainText'      => $post->post_content,
			'contentSize'    => str_word_count( strip_tags( $post->post_content ) ),
			'sectionName'    => get_section_name( $post ),
			// Optional fields
			'dateModified'   => $post->post_modified_gmt,
			'tags'           => get_post_tags( $post ),
			'canonicalURL'   => wp_get_canonical_url( $post ),
		]
	);
}

/**
 * Get section name from the post URL.
 * For example, example.com/news/politics, news would be the section name.
 * Not all content will have a section name.
 *
 * @param WP_Post $post Post object.
 *
 * @return string Section name.
 */
function get_section_name( $post ) {
	$permalink = get_permalink( $post );
	$permalink = parse_url( $permalink );
	$parts     = explode( '/', $permalink['path'] );

	if ( 1 === count( $parts ) ) {
		return '';
	}

	return array_slice( $parts, -2, 1 );
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
	return array_map(
		function( $tag ) {
			return $tag->name;
		},
		$tags
	);
}

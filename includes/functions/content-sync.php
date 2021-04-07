<?php
/**
 * Content sync
 *
 * @package SophiWP
 */

namespace SophiWP\ContentSync;

use WP_Error;

use function SophiWP\Settings\get_sophi_settings;
use function SophiWP\Core\get_supported_post_types;
use SophiWP\Utils;

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

	add_action( 'transition_post_status', $n( 'track_event' ), 10, 3 );
}

/**
 * Sending data to SnowPlow.
 *
 * @param string  $new_status New post status.
 * @param string  $old_status Old post status.
 * @param WP_Post $post       Post object.
 *
 * @return null|WP_Error
 */
function track_event( $new_status, $old_status, $post ) {
	$tracker = init_tracker();
	$action  = '';

	if ( ! in_array( $post->post_type, get_supported_post_types(), true ) ) {
		return new WP_Error(
			'sophi_unsupported_post_type',
			'This post type is not supported.'
		);
	}

	if ( is_wp_error( $tracker ) ) {
		return $tracker;
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
		return new WP_Error(
			'sophi_invalid_action',
			'The publishing action is invalid.'
		);
	}

	$data           = get_post_data( $post );
	$data['action'] = $action;

	$tracker->trackUnstructEvent(
		[
			'schema' => 'iglu:com.sophi/content_update/jsonschema/1-0-3',
			'data'   => $data,
		],
		[
			[
				'schema' => 'iglu:com.globeandmail/environment/jsonschema/1-0-9',
				'data'   => [
					'environment' => get_sophi_settings( 'environment' ),
					'client'      => get_sophi_settings( 'tracker_client_id' ),
				],
			],
		]
	);
}

/**
 * Initialize Snowplow tracker.
 *
 * @return Tracker|WP_Error
 */
function init_tracker() {
	$collector_url = get_sophi_settings( 'collector_url' );
	if ( ! $collector_url ) {
		return new WP_Error(
			'sophi_missing_collector_url',
			'The collector URL is missing.'
		);
	}

	$app_id  = sprintf( '%s-cms', get_sophi_settings( 'tracker_client_id' ) );
	$emitter = new SyncEmitter( $collector_url, 'https', 'POST', 1, false );
	$subject = new Subject();
	return new Tracker( $emitter, $subject, 'sophiTag', $app_id, false );
}

/**
 * Prepare post data to send to Snowplow.
 *
 * @param WP_Post $post Post object.
 *
 * @return array
 */
function get_post_data( $post ) {
	$content = apply_filters( 'the_content', get_the_content( null, false, $post ) );
	$content = str_replace( ']]>', ']]&gt;', $content );

	$data = [
		'contentId'      => strval( $post->ID ),
		'headline'       => get_the_title( $post ),
		'byline'         => [ get_the_author_meta( 'display_name', $post->post_author ) ],
		'accessCategory' => 'free access',
		'datePublished'  => gmdate( \DateTime::RFC3339, strtotime( $post->post_date_gmt ) ),
		'plainText'      => wp_strip_all_tags( $content ),
		'contentSize'    => str_word_count( wp_strip_all_tags( $content ) ),
		'sectionName'    => Utils\get_section_name( Utils\get_breadcrumb( $post ) ),
		// Optional fields
		'dateModified'   => gmdate( \DateTime::RFC3339, strtotime( $post->post_modified_gmt ) ),
		'tags'           => Utils\get_post_tags( $post ),
		'canonicalURL'   => wp_get_canonical_url( $post ),
	];

	// Remove empty key.
	$data = array_filter( $data );
	/**
	 * Filter post data for content sync events (CMS updates).
	 *
	 * @since 1.0.0
	 * @hook sophi_post_data
	 *
	 * @param {array} $post_data Formatted post data.
	 *
	 * @return {array} Formatted post data.
	 */
	return apply_filters( 'sophi_post_data', $data );
}

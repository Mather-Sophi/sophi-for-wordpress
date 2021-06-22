<?php
/**
 * Content sync
 *
 * @package SophiWP
 */

namespace SophiWP\ContentSync;

use WP_Error;

use function SophiWP\Settings\get_sophi_settings;
use function SophiWP\Utils\get_supported_post_types;
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
			'schema' => 'iglu:com.sophi/content_update/jsonschema/2-0-0',
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
	$collector_url     = get_sophi_settings( 'collector_url' );
	$tracker_client_id = get_sophi_settings( 'tracker_client_id' );

	if ( ! $collector_url ) {
		return new WP_Error(
			'sophi_missing_collector_url',
			'The collector URL is missing.'
		);
	}

	if ( ! $tracker_client_id ) {
		return new WP_Error(
			'sophi_missing_tracker_client_id',
			'The Tracker Client ID is missing.'
		);
	}

	$app_id  = sprintf( '%s-cms', $tracker_client_id );
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

	/**
	 * Filter data type of the given post.
	 *
	 * @since 1.0.0
	 * @hook sophi_post_data_type
	 *
	 * @param {string}  $type Post data type, one of article|video|audio|image
	 * @param {WP_Post} $post WP_Post object.
	 *
	 * @return {string} Post data type.
	 */
	$type = apply_filters( 'sophi_post_data_type', get_post_format( $post ), $post );

	if ( ! in_array( $type, [ 'video', 'audio', 'image' ], true ) ) {
		$type = 'article';
	}

	$canonical_url = wp_get_canonical_url( $post );

	// Support Yoast SEO canonical URL.
	if ( class_exists( 'WPSEO_Options' ) ) {
		$yoast_canonical = get_post_meta( $post->ID, '_yoast_wpseo_canonical', true );
		if ( $yoast_canonical ) {
			$canonical_url = $yoast_canonical;
		}
	}

	/**
	 * Filter canonical URL of the given post.
	 *
	 * @since 1.0.4
	 * @hook sophi_post_canonical_url
	 *
	 * @param {string}  $canonical_url Canonical URL of given post.
	 * @param {WP_Post} $post WP_Post object.
	 *
	 * @return {string} Canonical URL.
	 */
	$canonical_url = apply_filters( 'sophi_post_canonical_url', $canonical_url, $post );

	$data = [
		'contentId'      => strval( $post->ID ),
		'headline'       => get_the_title( $post ),
		'byline'         => [ get_the_author_meta( 'display_name', $post->post_author ) ],
		'accessCategory' => 'free access',
		'publishedAt'    => gmdate( \DateTime::RFC3339, strtotime( $post->post_date_gmt ) ),
		'plainText'      => wp_strip_all_tags( $content ),
		'contentSize'    => str_word_count( wp_strip_all_tags( $content ) ),
		'sectionNames'   => Utils\get_section_names( Utils\get_post_breadcrumb( $post ) ),
		'modifiedAt'     => gmdate( \DateTime::RFC3339, strtotime( $post->post_modified_gmt ) ),
		'tags'           => Utils\get_post_tags( $post ),
		'url'            => get_permalink( $post ),
		'type'           => $type,
		'isCanonical'    => untrailingslashit( $canonical_url ) === untrailingslashit( get_permalink( $post ) ),
		'promoImageUri'  => get_the_post_thumbnail_url( $post, 'full' ),
	];

	// Remove empty key.
	$data = array_filter( $data );
	/**
	 * Filter post data for content sync events (aka "CMS updates" in Sophi.io terms) sent to Sophi Collector.  This allows control over data before it is sent to Collector in case it needs to be modified for unique site needs.  Note that if you add, change, or remove any fields with this that those changes will need to be coordinated with the Sophi.io team to ensure content is appropriately received by Collector.
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

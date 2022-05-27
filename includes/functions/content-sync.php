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
use SophiWP\Emitter;
use function SophiWP\Utils\get_wp_sophi_versions;

/**
 * Default setup routine
 *
 * @return void
 */
function setup() {
	$n = function( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'wp_after_insert_post', $n( 'track_event' ), 10, 4 );
}

/**
 * Sending data to SnowPlow.
 *
 * @param int          $post_id     Post ID.
 * @param WP_Post      $post        Post object.
 * @param bool         $update      Whether this is an existing post being updated.
 * @param null|WP_Post $post_before Null for new posts, the WP_Post object prior
 *                                  to the update for updated posts.
 *
 * @return null|WP_Error
 */
function track_event( $post_id, $post, $update, $post_before ) {

	$new_status = $post->post_status;
	$old_status = $post_before ? $post_before->post_status : '';

	// Don't send any event if the page is assigned to the front page or posts page
	if ( $post_id === (int) get_option( 'page_on_front' ) || $post_id === (int) get_option( 'page_for_posts' ) ) {
		return;
	}

	// Don't send any event when creating new article.
	if ( 'auto-draft' === $new_status || 'inherit' === $new_status ) {
		return;
	}

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

	if ( class_exists( 'WPSEO_Meta' ) ) {
		// Detect if the current request comes from Quick Edit.
		if (
			! empty( $_POST['_inline_edit'] )
			&& wp_verify_nonce( sanitize_text_field( $_POST['_inline_edit'] ), 'inlineeditnonce' )
			&& ! empty( $_POST['action'] )
			&& 'inline-save' === $_POST['action']
		) {
			return send_track_event( $tracker, $post, $action );
		}

		$pending_action = get_transient( 'sophi_content_sync_pending_' . $post->ID );

		// Only set temporary action when publishing content
		if ( ! $pending_action && 'publish' === $action ) {
			set_transient( 'sophi_content_sync_pending_' . $post->ID, $action, MINUTE_IN_SECONDS );
		}

		return send_track_event( $tracker, $post, $action );
	}

	send_track_event( $tracker, $post, $action );
}

/**
 * Send the track event to Sophi SnowPlow server.
 *
 * @since 1.0.4
 *
 * @param Tracker $tracker Tracker object.
 * @param WP_Post $post    WP_Post object.
 * @param string  $action  Publishing action.
 */
function send_track_event( $tracker, $post, $action ) {
	$pending_action = get_transient( 'sophi_content_sync_pending_' . $post->ID );
	$data           = get_post_data( $post );
	$data['action'] = $action;

	if ( $pending_action ) {
		$data['action'] = $pending_action;
		delete_transient( 'sophi_content_sync_pending_' . $post->ID );
	}

	/**
	 * Filters the data used in Sophi track event request.
	 *
	 * @since 1.0.14
	 * @hook sophi_cms_tracking_request_data
	 *
	 * @param {array}   $data    Tracking data to send.
	 * @param {Tracker} $tracker Tracker being used.
	 * @param {string}  $post    Post object.
	 * @param {string}  $action  Publishing action.
	 * 
	 * @return {array} Tracking data to send.
	 */
	$data = apply_filters_ref_array( 'sophi_cms_tracking_request_data', array( $data, &$tracker, $post, $action ) );

	/** This filter is documented in includes/functions/content-sync.php */
	$debug = apply_filters( 'sophi_tracker_emitter_debug', false );

	// Suppress stdout from Emitters in debug mode.
	if ( true === $debug ) {
		ob_start();
	}

	$tracker->trackUnstructEvent(
		[
			'schema' => 'iglu:com.sophi/content_update/jsonschema/2-0-3',
			'data'   => $data,
		],
		[
			[
				'schema' => 'iglu:com.globeandmail/environment/jsonschema/1-0-9',
				'data'   => [
					'environment' => get_sophi_settings( 'environment' ),
					'client'      => get_sophi_settings( 'tracker_client_id' ),
					'version'     => get_wp_sophi_versions()
				],
			],
		]
	);

	if ( true === $debug ) {
		ob_end_clean();
	}

	/**
	 * Fires after tracker sends the request.
	 *
	 * @since 1.0.14
	 * @hook sophi_cms_tracking_result
	 *
	 * @param {array}   $data    Tracked data.
	 * @param {Tracker} $tracker Tracker object.
	 * @param {WP_Post} $post    Post object.
	 * @param {string}  $action  Publishing action.
	 */
	do_action_ref_array( 'sophi_cms_tracking_result', array( $data, &$tracker, $post, $action ) );
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

	/**
	 * Whether to turn on emitter debug
	 *
	 * @since 1.0.14
	 * @hook sophi_tracker_emitter_debug
	 *
	 * @param {bool} $debug Debug is active.
	 * 
	 * @return {bool} Whether to turn on emitter debug.
	 */
	$debug = apply_filters( 'sophi_tracker_emitter_debug', false );

	$app_id  = sprintf( '%s:cms', $tracker_client_id );
	$emitter = new Emitter( $collector_url, 'https', 'POST', 1, $debug );
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
	$content       = apply_filters( 'the_content', get_the_content( null, false, $post ) );
	$content       = str_replace( ']]>', ']]&gt;', $content );
	$canonical_url = wp_get_canonical_url( $post );
	$keywords      = [];
	$permalink     = get_permalink( $post );

	// Support Yoast SEO canonical URL and focus keyphrase.
	if ( class_exists( 'WPSEO_Meta' ) ) {
		$yoast_canonical = get_post_meta( $post->ID, '_yoast_wpseo_canonical', true );
		if ( $yoast_canonical ) {
			$canonical_url = $yoast_canonical;
		}

		$yoast_focuskw = get_post_meta( $post->ID, '_yoast_wpseo_focuskw', true );
		if ( ! empty( $yoast_focuskw ) ) {
			// Limit focus keyphrase to max length of 128.
			$keywords = [ substr( $yoast_focuskw, 0, 128 ) ];
		}
	}

	$parsed_url = wp_parse_url( $permalink );
	$hostname   = $parsed_url['host'] ?? '';
	$path       = $parsed_url['path'] ?? '';

	$data = [
		'contentId'           => strval( $post->ID ),
		'headline'            => get_the_title( $post ),
		'byline'              => [ get_the_author_meta( 'display_name', $post->post_author ) ],
		'accessCategory'      => 'free access',
		'publishedAt'         => gmdate( \DateTime::RFC3339, strtotime( $post->post_date_gmt ) ),
		'plainText'           => wp_strip_all_tags( $content ),
		'size'                => str_word_count( wp_strip_all_tags( $content ) ),
		'allSections'         => Utils\get_post_categories_paths( $post->ID ),
		'sectionNames'        => Utils\get_post_categories( $post->ID ),
		'modifiedAt'          => gmdate( \DateTime::RFC3339, strtotime( $post->post_modified_gmt ) ),
		'tags'                => Utils\get_post_tags( $post ),
		'url'                 => $permalink,
		'type'                => Utils\get_post_content_type( $post ),
		'promoImageUri'       => '',
		'thumbnailImageUri'   => get_the_post_thumbnail_url( $post, 'full' ),
		'embeddedImagesCount' => Utils\get_number_of_embedded_images( $content ),
		'classificationCode'  => '',
		'collectionName'      => '',
		'isSponsored'         => false,
		'promoPlainText'      => '',
		'keywords'            => $keywords,
		'creditLine'          => '',
		'ownership'           => '',
		'editorialAccessName' => '',
		'subtype'             => '',
		'redirectToUrl'       => '',
		'hostname'            => $hostname,
		'path'                => $path,
	];

	$data = array_filter( $data );

	// Add canonical after filtering the falsy items.
	$data['isCanonical'] = untrailingslashit( $canonical_url ) === untrailingslashit( $permalink );

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

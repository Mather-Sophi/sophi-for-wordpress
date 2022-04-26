<?php
/**
 * Sophi CLI Command.
 *
 * @package SophiWP
 */

namespace SophiWP;

use WP_CLI;

use function SophiWP\Utils\get_supported_post_types;
use function SophiWP\ContentSync\track_event;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( class_exists( 'WPCOM_VIP_CLI_Command' ) ) {
	class Base_CLI_Command extends \WPCOM_VIP_CLI_Command {}
} else {
	class Base_CLI_Command extends \WP_CLI_Command { // phpcs:ignore

		/**
		 *  Clear all of the caches for memory management
		 */
		protected function stop_the_insanity() {
			global $wpdb, $wp_object_cache;

			/**
			 * Reset the WordPress DB query log
			 */
			$wpdb->queries = array();


			/**
			 * Reset the local WordPress object cache
			 *
			 * This only cleans the local cache in WP_Object_Cache, without
			 * affecting memcache
			 */
			if ( ! is_object( $wp_object_cache ) ) {
				return;
			}

			$wp_object_cache->group_ops = array();
			$wp_object_cache->memcache_debug = array();
			$wp_object_cache->cache = array();

			if ( is_callable( $wp_object_cache, '__remoteset' ) ) {
				$wp_object_cache->__remoteset(); // important
			}
		}
	}
}

/**
 * Class: Sophi CLI Command.
 */
class Command extends Base_CLI_Command {

	/**
	 * Sync all existing content to Sophi Collector.
	 *
	 * [--limit=<number>]
	 * : Limit the amount of posts to be synced.
	 *
	 * [--per_page=<number>]
	 * : Number of posts to process each batch.
	 *
	 * [--post_types=<string>]
	 * : Post types to be processed. Comma separated for passing multiple post types.
	 *
	 * [--include=<number>]
	 * : Post IDs to process. Comma separated for passing multiple item.
	 *
	 * [--dry-run=<boolean>]
	 * : Whether to run command in the dry run mode. Default to false.
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Options.
	 */
	public function sync( $args, $assoc_args ) {
		if ( class_exists( 'WPCOM_VIP_CLI_Command' ) ) {
			$this->start_bulk_operation();
		}

		$per_page    = 50;
		$limit       = 0;
		$paged       = 1;
		$count       = 0;
		$error_count = 0;
		$dry_run     = false;
		$post_types  = get_supported_post_types();
		$include     = [];

		if ( ! empty( $assoc_args['limit'] ) ) {
			$_limit = intval( $assoc_args['limit'] );
			if ( $_limit ) {
				$limit = $_limit;
			}
		}

		if ( ! empty( $assoc_args['per_page'] ) ) {
			/**
			 * The maximum post per page CLI can process each batch.
			 * Default to 100.
			 *
			 * @since 1.0.0
			 * @hook sophi_cli_per_page_limit
			 *
			 * @param {integer} $limit Posts per page limit.
			 *
			 * @return {integer} Posts per page limit.
			 */
			$per_page_limit = apply_filters( 'sophi_cli_per_page_limit', 100 );
			$_per_page      = intval( $assoc_args['per_page'] );
			if ( $_per_page ) {
				if ( $_per_page > $per_page_limit ) {
					$per_page = $per_page_limit;
				} else {
					$per_page = $_per_page;
				}
			}
		}

		if ( ! empty( $assoc_args['post_types'] ) ) {
			$_post_types = explode( ',', $assoc_args['post_types'] );
			$post_types  = array_filter(
				$_post_types,
				function( $post_type ) use ( $post_types ) {
					return in_array( $post_type, $post_types, true );
				}
			);

			$unsupported_types = array_diff( $_post_types, get_supported_post_types() );

			if ( empty( $post_types ) ) {
				WP_CLI::error( 'No supported post types provided.' );
			}

			if ( ! empty( $unsupported_types ) ) {
				WP_CLI::warning( 'Skipping unsupported post types: ' . implode( ', ', $unsupported_types ) );
			}
		}

		if ( ! empty( $assoc_args['include'] ) ) {
			$_include = explode( ',', $assoc_args['include'] );
			$_include = array_filter( $_include, 'is_int' );
			if ( ! empty( $_include ) ) {
				$include = $_include;
			}
		}

		if ( isset( $assoc_args['dry-run'] ) ) {
			// Passing `--dry-run=false` to the command leads to the `false` value being set to string `'false'`, but casting `'false'` to bool produces `true`. Thus the special handling.
			if ( 'false' === $assoc_args['dry-run'] ) {
				$dry_run = false;
			} else {
				$dry_run = (bool) $assoc_args['dry-run'];
			}
		}

		// Counting posts to process.
		if ( ! $limit ) {
			$query_args = array(
				'posts_per_page'      => $per_page,
				'post_type'           => $post_types,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'suppress_filters'    => false,
			);
			if ( ! empty( $include ) ) {
				$query_args['post__in'] = $include;
			}

			$limit = ( new \WP_Query( $query_args ) )->found_posts;
		}

		if ( ! $limit ) {
			WP_CLI::error( 'No post found.' );
		}

		if ( $dry_run ) {
			WP_CLI::line( 'Running in dry-run mode.' );
		} else {
			WP_CLI::line( 'Running in live mode.' );
		}

		$progress = \WP_CLI\Utils\make_progress_bar( 'Syncing posts to Sophi', $limit );
		do {

			if ( ! empty( $include ) ) {
				// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
				$posts = get_posts(
					array(
						'posts_per_page'      => $per_page,
						'post_type'           => $post_types,
						'paged'               => $paged,
						'post_status'         => 'publish',
						'post__in'            => $include,
						'ignore_sticky_posts' => true,
						'suppress_filters'    => false,
					)
				);
			} else {
				// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_posts_get_posts
				$posts = get_posts(
					array(
						'posts_per_page'      => $per_page,
						'post_type'           => $post_types,
						'paged'               => $paged,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
						'suppress_filters'    => false,
					)
				);
			}

			foreach ( $posts as $post ) {
				if ( $count < $limit ) {
					if ( ! $dry_run ) {
						$response = track_event( $post->ID, $post, true, null );
						if ( is_wp_error( $response ) ) {
							$error_count++;
						} else {
							$count++;
						}
					} else {
						$count++;
					}
				}
			}

			$progress->tick( count( $posts ) );
			sleep(1);

			// Free up memory.
			$this->stop_the_insanity();

			$paged++;

			$continue = count( $posts ) === $per_page && $count < $limit;
		} while ( $continue );

		$progress->finish();

		if ( false === $dry_run ) {
			WP_CLI::success( sprintf( '%d posts have successfully been synced to Sophi Collector.', $count ) );
			if ( $error_count ) {
				WP_CLI::warning( sprintf( '%d posts have issues.', $error_count ) );
			}
		} else {
			WP_CLI::success( sprintf( '%d posts will be synced to Sophi Collector.', $count ) );
			if ( $error_count ) {
				WP_CLI::warning( sprintf( '%d posts have issues.', $error_count ) );
			}
		}

		if ( class_exists( 'WPCOM_VIP_CLI_Command' ) ) {
			$this->end_bulk_operation();
		}
	}
}

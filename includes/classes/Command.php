<?php
/**
 * Sophi CLI Command.
 *
 * @package SophiWP
 */

namespace SophiWP;

use WP_CLI;

use function SophiWP\Core\get_supported_post_types;
use function SophiWP\ContentSync\track_event;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WPCOM_VIP_CLI_Command' ) ) {
	return;
}

/**
 * Class: Sophi CLI Command.
 */
class Command extends \WPCOM_VIP_CLI_Command {

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
	 * @param array $args       Arguments.
	 * @param array $assoc_args Options.
	 */
	public function sync( $args, $assoc_args ) {
		$this->start_bulk_operation();

		$per_page    = 50;
		$limit       = 0;
		$paged       = 1;
		$count       = 0;
		$error_count = 0;
		$dry_run     = true;
		$post_types  = get_supported_post_types();
		$include     = [];

		if ( ! empty( $assoc_args['limit'] ) ) {
			$_limit = intval( $assoc_args['limit'] );
			if ( $_limit ) {
				$limit = $_limit;
			}
		}

		if ( ! empty( $assoc_args['per_page'] ) ) {
			$_per_page = intval( $assoc_args['per_page'] );
			if ( $_per_page ) {
				$per_page = $_per_page;
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

		if ( $dry_run ) {
			WP_CLI::line( 'Running in dry-run mode.' );
		} else {
			WP_CLI::line( 'Running in live mode.' );
		}

		do {

			if ( ! empty( $include ) ) {
				$posts = get_posts(
					array(
						'posts_per_page'      => $per_page,
						'post_type'           => get_supported_post_types(),
						'paged'               => $paged,
						'post_status'         => 'publish',
						'post__in'            => $include,
						'ignore_sticky_posts' => true,
					)
				);
			} else {
				$posts = get_posts(
					array(
						'posts_per_page'      => $per_page,
						'post_type'           => get_supported_post_types(),
						'paged'               => $paged,
						'post_status'         => 'publish',
						'ignore_sticky_posts' => true,
					)
				);
			}

			foreach ( $posts as $post ) {
				if ( 0 === $limit || $count < $limit ) {
					if ( ! $dry_run ) {
						$response = track_event( 'publish', 'publish', $post );
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

			// Pause.
			WP_CLI::line( 'Preparing for the next batch...' );
			sleep( 3 );

			// Free up memory.
			$this->stop_the_insanity();

			$paged++;

			$continue = count( $posts ) === $per_page && ( $limit > 0 || $count < $limit );
		} while ( $continue );

		if ( false === $dry_run ) {
			WP_CLI::success( sprintf( '%d posts have successfully been synced to Sophi Collector.', $count ) );
			if ( $error_count ) {
				WP_CLI::warning( sprintf( '%d posts have issues.', $count ) );
			}
		} else {
			WP_CLI::success( sprintf( '%d posts will be synced to Sophi Collector.', $count ) );
		}
	}
}

<?php
/**
 * Custom Sophi Emitter
 *
 * @package SophiWP
 */

namespace SophiWP;

use Snowplow\Tracker\Emitters\SyncEmitter;

/**
 * Extend Snowplow Emitter and overload it's filesystem
 * methods to disable 3rd-party debug logging
 */
class Emitter extends SyncEmitter {

	public function makeDir( $dir ) {
		return false;
	}

	public function openFile( $file_path ) {
		return false;
	}

	public function closeFile( $file_path ) {
		return false;
	}

	public function copyFile( $path_from, $path_to ) {
		return false;
	}

	public function deleteFile( $file_path ) {
		return false;
	}

	public function writeToFile( $file_path, $content ) {
		return false;
	}

}

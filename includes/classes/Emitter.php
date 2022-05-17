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
		return true;
	}

	public function openFile( $file_path ) {
		return true;
	}

	public function closeFile( $file_path ) {
		return true;
	}

	public function copyFile( $path_from, $path_to ) {
		return true;
	}

	public function deleteFile( $file_path ) {
		return true;
	}

	public function writeToFile( $file_path, $content ) {
		return true;
	}

}

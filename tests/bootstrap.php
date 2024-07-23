<?php
/**
 * WP Video Sync Tests: Bootstrap
 *
 * @package wp-video-sync
 */

// Ensure Composer autoloader is loaded.
require_once __DIR__ . '/../vendor/autoload.php';

\Mantle\Testing\manager()
	// Rsync the plugin to plugins/wp-video-sync when testing.
	->maybe_rsync_plugin()
	// Load the main file of the plugin.
	->loaded( fn () => require_once __DIR__ . '/../wp-video-sync.php' )
	->install();

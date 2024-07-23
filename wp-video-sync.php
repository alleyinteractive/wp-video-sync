<?php
/**
 * Plugin Name: WP Video Sync
 * Plugin URI: https://github.com/alleyinteractive/wp-video-sync
 * Description: Sync videos from a hosting provider to WordPress
 * Version: 0.1.0
 * Author: Alley
 * Author URI: https://github.com/alleyinteractive/wp-video-sync
 * Requires at least: 6.6
 * Tested up to: 6.6
 *
 * Text Domain: wp-video-sync
 * Domain Path: /languages/
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/src/autoload.php';

<?php
/**
 * WP Video Sync: Autoloader
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync;

/**
 * Autoload classes.
 *
 * @param string $class Class name.
 */
function autoload( string $class ): void {
	// Only autoload classes for this namespace.
	$class = ltrim( $class, '\\' );
	if ( strpos( $class, __NAMESPACE__ . '\\' ) !== 0 ) {
		return;
	}

	$class = strtolower( str_replace( [ __NAMESPACE__ . '\\', '_' ], [ '', '-' ], $class ) );
	$dirs  = explode( '\\', $class );
	$class = array_pop( $dirs );

	// Negotiate filename.
	$filename = ! in_array( 'interfaces', $dirs, true )
		? 'class-' . $class . '.php'
		: $class . '.php';

	require_once __DIR__ . '/' . implode( '/', $dirs ) . '/' . $filename;
}

spl_autoload_register( '\Alley\WP\WP_Video_Sync\autoload' );

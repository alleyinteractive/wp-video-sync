<?php
/**
 * WP Video Sync Mocks: JW Player 7 for WP (free and premium)
 *
 * phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound,WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
 *
 * @package wp-video-sync
 */

/**
 * A mock for the JW Player 7 for WP (free and premium) dashboard API class.
 */
class JWPPP_Dashboard_Api {
	/**
	 * The API call
	 *
	 * @param string $endpoint the endpoint.
	 *
	 * @return mixed
	 */
	public function call( $endpoint ) {
		$output = wp_remote_get( 'https://api.jwplayer.com/v2/sites/example-api-key/' . $endpoint );
		return json_decode( $output['body'] ) ?? [];
	}
}

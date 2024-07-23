<?php
/**
 * WP Video Sync: JW Player 7 for WP Adapter
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use DateTimeImmutable;
use stdClass;

/**
 * JW Player 7 for WP Adapter. Supports both the free and premium versions of the plugin.
 */
class JW_Player_7_For_WP implements Adapter {
	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 *
	 * @return stdClass[] An array of video data.
	 */
	public function get_videos( DateTimeImmutable $updated_after ): array {
		// Check if the JW Player 7 for WP Premium plugin is active.
		if ( class_exists( 'JWPPP_Dashboard_Api' ) ) {
			$api    = new \JWPPP_Dashboard_Api();
			$result = $api->call(
				sprintf(
					'media/?q=last_modified:[%s TO *]&page=1&page_length=100&sort=last_modified:asc',
					$updated_after->format( 'Y-m-d' )
				)
			);
			return $result->media ?? [];
		}

		// TODO: Check if the free version of the plugin is active.

		return [];
	}
}

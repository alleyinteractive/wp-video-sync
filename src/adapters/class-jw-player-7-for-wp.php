<?php
/**
 * WP Video Sync: JW Player 7 for WP Adapter
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use Alley\WP\WP_Video_Sync\Last_Modified_Date;
use DateTimeImmutable;
use stdClass;

/**
 * JW Player 7 for WP Adapter. Supports both the free and premium versions of the plugin.
 */
class JW_Player_7_For_WP extends Last_Modified_Date implements Adapter {

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 * @param int               $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return stdClass[] An array of video data.
	 */
	public function get_videos( DateTimeImmutable $updated_after, int $batch_size ): array {
		// Check if the JW Player 7 for WP plugin is active (free or premium).
		if ( class_exists( 'JWPPP_Dashboard_Api' ) ) {
			$api    = new \JWPPP_Dashboard_Api();
			$result = $api->call(
				sprintf(
					'media/?q=last_modified:[%s TO *]&page=1&page_length=%d&sort=last_modified:asc',
					$updated_after->format( 'Y-m-d' ),
					$batch_size
				)
			);
			$videos = $result->media ?? [];

			// Attempt to set the last modified date.
			if ( isset( $videos[ count( $videos ) - 1 ]->last_modified ) ) {
				$last_modified_date = DateTimeImmutable::createFromFormat( DATE_W3C, $videos[ count( $videos ) - 1 ]->last_modified );
				if ( $last_modified_date instanceof DateTimeImmutable ) {
					$this->set_last_modified_date( $last_modified_date );
				}
			}

			return $videos;
		}

		return [];
	}
}

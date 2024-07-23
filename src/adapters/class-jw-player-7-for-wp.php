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
	 * The date of the last modification to the last batch of videos.
	 *
	 * @var ?DateTimeImmutable
	 */
	private ?DateTimeImmutable $last_modified_date;

	/**
	 * Fetches the date of the last modification to the last batch of videos.
	 *
	 * @return ?DateTimeImmutable
	 */
	public function get_last_modified_date(): ?DateTimeImmutable {
		return $this->last_modified_date;
	}

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 *
	 * @return stdClass[] An array of video data.
	 */
	public function get_videos( DateTimeImmutable $updated_after ): array {
		// Check if the JW Player 7 for WP plugin is active (free or premium).
		if ( class_exists( 'JWPPP_Dashboard_Api' ) ) {
			$api    = new \JWPPP_Dashboard_Api();
			$result = $api->call(
				sprintf(
					'media/?q=last_modified:[%s TO *]&page=1&page_length=100&sort=last_modified:asc',
					$updated_after->format( 'Y-m-d' )
				)
			);
			$videos = $result->media ?? [];

			// Attempt to set the last modified date.
			if ( isset( $videos[ count( $videos ) - 1 ]->last_modified ) ) {
				$last_modified_date = DateTimeImmutable::createFromFormat( DATE_W3C, $videos[ count( $videos ) - 1 ]->last_modified );
				if ( $last_modified_date instanceof DateTimeImmutable ) {
					$this->last_modified_date = $last_modified_date;
				}
			}

			return $videos;
		}

		return [];
	}
}

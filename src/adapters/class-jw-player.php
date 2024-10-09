<?php
/**
 * WP Video Sync: JW Player Adapter.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\API\JW_Player_API;
use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use Alley\WP\WP_Video_Sync\Last_Modified_Date;
use DateTimeImmutable;

/**
 * JW Player Adapter.
 */
class JW_Player extends Last_Modified_Date implements Adapter {

	/**
	 * Constructor.
	 *
	 * @param string $api_key The API key.
	 * @param string $api_secret The API v2 secret key.
	 */
	public function __construct(
		public readonly string $api_key,
		public readonly string $api_secret
	) {}

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 * @param int               $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return array<mixed> An array of video data objects.
	 */
	public function get_videos( DateTimeImmutable $updated_after, int $batch_size ): array {
		$videos = (
			new JW_Player_API(
				$this->api_key,
				$this->api_secret
			)
		)->get_videos_after(
			$updated_after,
			$batch_size
		);

		// Check for an API error.
		if ( ! empty( $videos['error'] ) ) {
			return [];
		}

		// Validate the media property.
		if ( ! is_array( $videos['media'] ) ) {
			return [];
		}

		// Attempt to set the last modified date.
		if (
			! empty( $videos['media'][ count( $videos['media'] ) - 1 ] )
			&& isset( $videos['media'][ count( $videos['media'] ) - 1 ]->last_modified )
		) {
			$last_modified_date = DateTimeImmutable::createFromFormat( DATE_W3C, $videos['media'][ count( $videos['media'] ) - 1 ]->last_modified );
			if ( $last_modified_date instanceof DateTimeImmutable ) {
				$this->set_last_modified_date( $last_modified_date );
			}
		}

		// Return the videos.
		return ! empty( $videos['media'] ) ? $videos['media'] : [];
	}
}

<?php
/**
 * WP Video Sync: JW Player Adapter.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\API\JW_Player_API;
use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use DateTimeImmutable;
use stdClass;

/**
 * JW Player Adapter.
 */
class JW_Player implements Adapter {

	/**
	 * The date of the last modification to the last batch of videos.
	 *
	 * @var ?DateTimeImmutable
	 */
	private ?DateTimeImmutable $last_modified_date;

	/**
	 * The JW Player API.
	 *
	 * @var JW_Player_API
	 */
	public JW_Player_API $jw_player_api;

	/**
	 * The start date if the `last_modified_date` does not exist.
	 *
	 * @var string
	 */
	public string $origin_modified_date;

	/**
	 * Constructor.
	 *
	 * @param JW_Player_API $api Instance of the JW Player API object.
	 * @param string        $origin_modified_date The date of the used if the `last_modified_date` does not exist.
	 */
	public function __construct( JW_Player_API $api, string $origin_modified_date = '' ) {
		$this->jw_player_api        = $api;
		$this->origin_modified_date = ! empty( $origin_modified_date ) ? $origin_modified_date : date( 'Y-m-d' );
	}

	/**
	 * Fetches the date of the last modification to the last batch of videos.
	 *
	 * @return ?DateTimeImmutable
	 */
	public function get_last_modified_date(): ?DateTimeImmutable {
		return $this->last_modified_date ?? DateTimeImmutable::createFromFormat('Y-m-d', $this->origin_modified_date );
	}

	/**
	 * Sets the date of the last modification to the latest batch of videos.
	 *
	 * @param array $videos An array of videos and associated data.
	 * @return void
	 */
	public function set_last_modified_date( array $videos ): void {
		if (
			! empty( $videos )
			&& isset( $videos[ count( $videos ) - 1 ]->last_modified )
		) {
			$last_modified_date = DateTimeImmutable::createFromFormat( DATE_W3C, $videos[ count( $videos ) - 1 ]->last_modified );

			if ( $last_modified_date instanceof DateTimeImmutable ) {
				$this->last_modified_date = $last_modified_date;
			}
		}
	}

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 * @param int               $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return stdClass[] An array of video data.
	 */
	public function get_videos( DateTimeImmutable $updated_after, int $batch_size ): array {
		// Get the latest videos from JW Player.
		$videos = $this->jw_player_api->request_latest_videos(
			$updated_after->format( 'Y-m-d' ),
			$batch_size
		);

		// Check for an API error.
		if ( ! empty( $videos['error'] ) ) {
			return [];
		}

		// Attempt to set the last modified date.
		$this->set_last_modified_date( $videos );

		// Return the videos.
		return ! empty( $videos ) ? $videos : [];
	}
}

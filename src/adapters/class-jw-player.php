<?php
/**
 * WP Video Sync: JW Player Adapter.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\API\JW_Player_API;
use Alley\WP\WP_Video_Sync\API\Request;
use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use Alley\WP\WP_Video_Sync\Last_Modified_Date;
use DateTimeImmutable;

/**
 * JW Player Adapter.
 */
class JW_Player extends Last_Modified_Date implements Adapter {

	/**
	 * The JW Player API.
	 *
	 * @var JW_Player_API
	 */
	public JW_Player_API $jw_player_api;

	/**
	 * Constructor.
	 *
	 * @param JW_Player_API $api Instance of the JW Player API object.
	 */
	public function __construct( JW_Player_API $api ) {
		$this->jw_player_api = $api;
	}

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 * @param int               $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return array<mixed> An array of video data objects.
	 */
	public function get_videos( DateTimeImmutable $updated_after, int $batch_size ): array {
		// Set the request URL based on the arguments.
		$this->jw_player_api->set_request_url(
			$updated_after->format( 'Y-m-d' ),
			$batch_size
		);

		// Perform the request.
		$videos = ( new Request( $this->jw_player_api ) )->get();

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
			$this->set_last_modified_date( $videos['media'][ count( $videos ) - 1 ]->last_modified );
		}

		// Return the videos.
		return ! empty( $videos['media'] ) ? $videos['media'] : [];
	}
}

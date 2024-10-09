<?php
/**
 * WP Video Sync: JW Player API integration.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\API;

use Alley\WP\WP_Video_Sync\Interfaces\API_Requester;
use Alley\WP\WP_Video_Sync\Last_Modified_Date;
use DateTimeImmutable;

/**
 * JW Player API.
 */
class JW_Player_API extends Last_Modified_Date implements API_Requester {

	/**
	 * The API URL.
	 *
	 * @var string
	 */
	public string $api_url = 'https://api.jwplayer.com/v2/sites';

	/**
	 * The request URL.
	 *
	 * @var string
	 */
	public string $request_url;

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
	 * Generate the request URL.
	 *
	 * @param string $last_modified_date The date of the last modification to the last batch of videos.
	 * @param int    $batch_size         The number of videos to fetch in each batch.
	 */
	public function set_request_url( string $last_modified_date, int $batch_size ): void {
		$request_url = $this->api_url . '/' . $this->api_key . '/media/';

		$this->request_url = add_query_arg(
			[
				'q'           => 'last_modified:[' . $last_modified_date . ' TO *]',
				'page'        => 1,
				'sort'        => 'last_modified:asc',
				'page_length' => $batch_size,
			],
			$request_url
		);
	}

	/**
	 * Get the request URL.
	 *
	 * @return string
	 */
	public function get_request_url(): string {
		return $this->request_url;
	}

	/**
	 * Get the request arguments.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function get_request_args(): array {
		return [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->api_secret,
				'Content-Type'  => 'application/json',
			],
		];
	}

	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 * @param int               $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return array<mixed> An array of video data objects.
	 */
	public function get_videos_after( DateTimeImmutable $updated_after, int $batch_size ): array {
		// Set the request URL based on the arguments.
		$this->set_request_url(
			$updated_after->format( 'Y-m-d' ),
			$batch_size
		);

		// Perform the request.
		return ( new Request( $this ) )->get();
	}

	/**
	 * Parse the API error response.
	 *
	 * @param array<mixed> $response_object The API response object.
	 *
	 * @return array<string, string>
	 */
	public function parse_error( array $response_object ): array {
		return ! empty( $response_object['errors'] )
			&& is_array( $response_object['errors'] )
			&& isset( $response_object['errors'][0]->description )
			? [ 'error' => $response_object['errors'][0]->description ]
			: [];
	}

	/**
	 * Parse the API successful response.
	 *
	 * @param array<mixed> $response_object The API response object.
	 *
	 * @return array<string, mixed>
	 */
	public function parse_success( array $response_object ): array {
		return ! empty( $response_object['media'] )
			&& is_array( $response_object['media'] )
			? [ 'media' => $response_object['media'] ]
			: [];
	}
}

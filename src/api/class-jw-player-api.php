<?php
/**
 * WP Video Sync: JW Player API integration.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\API;

use Alley\WP\WP_Video_Sync\Interfaces\API_Requester;

/**
 * JW Player API.
 */
class JW_Player_API implements API_Requester {

	/**
	 * The API URL.
	 *
	 * @var string
	 */
	public string $api_url = 'https://api.jwplayer.com/v2/sites';

	/**
	 * The API public key.
	 *
	 * @var string
	 */
	public string $api_key;

	/**
	 * The API v2 secret key.
	 *
	 * @var string
	 */
	public string $api_secret;

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
	 * @param string $api_secret The API secret.
	 */
	public function __construct( string $api_key, string $api_secret ) {
		$this->api_key    = $api_key;
		$this->api_secret = $api_secret;
	}

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

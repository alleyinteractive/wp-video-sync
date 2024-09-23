<?php
/**
 * WP Video Sync: JW Player API integration.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\API;

class JW_Player_API {

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
	 * Constructor.
	 */
	public function __construct( string $api_key, string $api_secret ) {
		$this->api_key    = $api_key;
		$this->api_secret = $api_secret;
	}

	/**
	 * Set the user agent in the request headers for identification purposes.
	 *
	 * @return string
	 */
	public function user_agent(): string {
		global $wp_version;

		return 'WordPress/' . $wp_version . ' WPVideoSync/' . WP_VIDEO_SYNC_VERSION . ' PHP/' . phpversion();
	}

	/**
	 * Generate the request URL.
	 *
	 * @param string $last_modified_date The date of the last modification to the last batch of videos.
	 * @param int    $batch_size         The number of videos to fetch in each batch.
	 *
	 * @return string
	 */
	public function request_url( string $last_modified_date, int $batch_size ): string {
		$request_url = $this->api_url . '/' . $this->api_key . '/media/';

		return add_query_arg(
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
	 * Generate the request arguments.
	 *
	 * @param string $type The type of request to make.
	 *
	 * @return array
	 */
	public function request_args( string $type = '' ): array {
		return [
			'user-agent' => $this->user_agent(),
			'headers'    => [
				'Authorization' => 'Bearer ' . $this->api_secret,
				'Content-Type'  => 'application/json',
			],
		];
	}

	/**
	 * Parse the API response.
	 *
	 * @param mixed $api_response The API response.
	 *
	 * @return array
	 */
	public function parse_response( mixed $api_response ): array {
		// Failed request expressed as a WP_Error.
		if ( is_wp_error( $api_response ) ) {
			return [];
		}

		// Condition for when the response body is empty.
		$response_body = wp_remote_retrieve_body( $api_response );

		if ( empty( $response_body ) ) {
			return [];
		}

		// Assign the response object for further evaluation.
		$response_object = json_decode( $response_body );

		if ( 200 !== wp_remote_retrieve_response_code( $api_response ) ) {
			return isset( $response_object->errors[0]->description )
				? [ 'error' => $response_object->errors[0]->description ]
				: [];
		}

		return is_array( $response_object->media ) && ! empty( $response_object->media )
			? $response_object->media
			: [];
	}

	/**
	 * Make the API request.
	 *
	 * @param string $updated_after The date of the last modification to the last batch of videos.
	 * @param int    $batch_size    The number of videos to fetch in each batch.
	 *
	 * @return array
	 */
	public function request_latest_videos( string $updated_after, int $batch_size ): array {
		$request_url = $this->request_url(
			$updated_after,
			$batch_size
		);

		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$api_request = vip_safe_wp_remote_get(
				$request_url,
				'',
				3,
				5,
				3,
				$this->request_args()
			);
		} else {
			$api_request = wp_remote_get(
				$request_url,
				wp_parse_args(
					$this->request_args(),
					[
						'timeout' => 3,
					]
				)
			);
		}

		return $this->parse_response( $api_request );
	}
}

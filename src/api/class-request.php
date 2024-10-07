<?php
/**
 * WP Video Sync: API request integration.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\API;

use Alley\WP\WP_Video_Sync\Interfaces\API_Requester;

/**
 * Perform an API request.
 */
class Request {

	/**
	 * Constructor.
	 *
	 * @param API_Requester $api_requester Instance of the API requester.
	 */
	public function __construct( public readonly API_Requester $api_requester ) {}

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
	 * Get the request arguments.
	 *
	 * @return array<string, mixed>
	 */
	public function get_request_args(): array {
		$requester_args = $this->api_requester->get_request_args();

		if ( empty( $requester_args['user-agent'] ) ) {
			$requester_args['user-agent'] = $this->user_agent();
		}

		/**
		 * Allow the request arguments to be filtered before the request is made.
		 *
		 * @param array<string, string|float|int|bool|array> $requester_args The request arguments.
		 */
		return apply_filters( 'wp_video_sync_request_args', $requester_args );
	}

	/**
	 * Parse the API response.
	 *
	 * @param mixed $response The API response.
	 *
	 * @return array<string, mixed>
	 */
	private function parse_response( mixed $response ): array {
		// Failed request expressed as a WP_Error.
		if ( is_wp_error( $response ) || empty( $response ) || ! is_array( $response ) ) {
			return [];
		}

		// Condition for when the response body is empty.
		$response_body = wp_remote_retrieve_body( $response );

		if ( empty( $response_body ) ) {
			return [];
		}

		// Assign the response object for further evaluation.
		$response_object = (array) json_decode( $response_body );

		// Explicitly state the results based on response code.
		return 200 === wp_remote_retrieve_response_code( $response )
			? $this->api_requester->parse_success( $response_object )
			: $this->api_requester->parse_error( $response_object );
	}

	/**
	 * Perform a GET request.
	 *
	 * @return array<string, mixed>
	 */
	public function get(): array {
		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$api_request = vip_safe_wp_remote_get(
				$this->api_requester->get_request_url(),
				'',
				3,
				5,
				3,
				$this->get_request_args()
			);
		} else {
			// Get the base request arguments.
			$custom_args = $this->get_request_args();

			// Explicitly define the request arguments.
			// NOTE: this is addressing an error stemming from PHPStan where the passed arguments to the `wp_remote_get()` were not accepted.
			$request_args = [];

			// Request method.
			if ( ! empty( $custom_args['method'] ) && is_string( $custom_args['method'] ) ) {
				$request_args['method'] = $custom_args['method'];
			}

			// Request headers.
			if (
				! empty( $custom_args['headers'] )
				&& (
					is_string( $custom_args['headers'] )
					|| is_array( $custom_args['headers'] )
				)
			) {
				$request_args['headers'] = $custom_args['headers'];
			}

			// Request timeout.
			if ( ! empty( $custom_args['timeout'] ) && is_float( $custom_args['timeout'] ) ) {
				$request_args['timeout'] = $custom_args['timeout'];
			}

			// Request user agent.
			if ( ! empty( $custom_args['user-agent'] ) && is_string( $custom_args['user-agent'] ) ) {
				$request_args['user-agent'] = $custom_args['user-agent'];
			}

			// Perform the request.
			$api_request = wp_remote_get( // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
				$this->api_requester->get_request_url(),
				array_filter( $request_args )
			);
		}

		return $this->parse_response( $api_request );
	}
}

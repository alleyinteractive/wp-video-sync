<?php
/**
 * WP Video Sync: API Requester Interface
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Interfaces;

/**
 * Defines an interface that all adapters must implement to
 * perform API requests that interact with `Request` class.
 */
interface API_Requester {

	/**
	 * Get the URL for the API request.
	 *
	 * @return string
	 */
	public function get_request_url(): string;

	/**
	 * Get the arguments for the API request.
	 *
	 * @return array
	 */
	public function get_request_args(): array;

	/**
	 * Parse a successful API response.
	 *
	 * @param array $response_object The response object.
	 * @return array
	 */
	public function parse_success( array $response_object ): array;

	/**
	 * Parse an error API response.
	 *
	 * @param array $response_object The response object.
	 * @return array
	 */
	public function parse_error( array $response_object ): array;
}

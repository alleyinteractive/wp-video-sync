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
	 * @return array<string, array<string, string>>
	 */
	public function get_request_args(): array;

	/**
	 * Parse an error API response.
	 *
	 * @param array<mixed> $response_object The API response object.
	 *
	 * @return array<string, string>
	 */
	public function parse_error( array $response_object ): array;

	/**
	 * Parse a successful API response.
	 *
	 * @param array<mixed> $response_object The API response object.
	 *
	 * @return array<string, mixed>
	 */
	public function parse_success( array $response_object ): array;
}

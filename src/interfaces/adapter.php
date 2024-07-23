<?php
/**
 * WP Video Sync: Adapter Interface
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Interfaces;

use DateTimeImmutable;

/**
 * Defines an interface that all adapters must implement.
 */
interface Adapter {
	/**
	 * Fetches videos from the provider that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 *
	 * @return array An array of video data. Specific shape will be determined by the adapter.
	 */
	public function get_videos( DateTimeImmutable $updated_after ): array;
}

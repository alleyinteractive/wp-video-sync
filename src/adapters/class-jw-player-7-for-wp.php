<?php
/**
 * WP Video Sync: JW Player 7 for WP Adapter
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Adapters;

use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use DateTimeImmutable;

/**
 * JW Player 7 for WP Adapter. Supports both the free and premium versions of the plugin.
 */
class JW_Player_7_For_WP implements Adapter {
	/**
	 * Fetches videos from JW Player that were modified after the provided DateTime.
	 *
	 * @param DateTimeImmutable $updated_after Return videos modified after this date.
	 *
	 * @return array An array of video data.
	 */
	public function get_videos( DateTimeImmutable $updated_after ): array {
		return [];
	}
}

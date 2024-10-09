<?php
/**
 * WP_Video_Sync: Last modified date.
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync;

use DateTimeImmutable;

/**
 * Class for tracking the modified date of the last video in a batch.
 */
class Last_Modified_Date {

	/**
	 * The date of the last modification to the last batch of videos.
	 *
	 * @var ?DateTimeImmutable
	 */
	private ?DateTimeImmutable $last_modified_date = null;

	/**
	 * Fetches the date of the last modification to the last batch of videos.
	 *
	 * @return ?DateTimeImmutable
	 */
	public function get_last_modified_date(): ?DateTimeImmutable {
		return $this->last_modified_date;
	}

	/**
	 * Sets the date of the last modification based on the latest batch of videos.
	 *
	 * @param DateTimeImmutable $last_modified_date The date of the last modified video in the batch.
	 * @return void
	 */
	public function set_last_modified_date( DateTimeImmutable $last_modified_date ): void {
		$this->last_modified_date = $last_modified_date;
	}
}

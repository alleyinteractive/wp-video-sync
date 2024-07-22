<?php
/**
 * WP_Video_Sync: Sync Manager
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync;

use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use DateTime;

/**
 * Sync manager. Manages the synchronization of videos from a provider.
 */
class Sync_Manager {
	/**
	 * The hook name for the cron job.
	 */
	const CRON_HOOK = 'wp_video_sync_cron';

	/**
	 * The option name for the last sync time.
	 */
	const LAST_SYNC_OPTION = 'wp_video_sync_last_sync';

	/**
	 * The adapter to use for fetching videos.
	 *
	 * @var Adapter
	 */
	public Adapter $adapter;

	/**
	 * The frequency with which to sync videos. Can be any valid value for wp_schedule_event().
	 *
	 * @var string
	 */
	public string $frequency = 'hourly';

	/**
	 * Constructor. Sets up actions and filters.
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'maybe_schedule_sync' ] );
		add_action( self::CRON_HOOK, [ $this, 'sync_videos' ] );
	}

	/**
	 * Allows the sync frequency to be set. Can be any valid value for wp_schedule_event().
	 *
	 * @param string $frequency The frequency with which to sync videos.
	 *
	 * @return $this For chaining configuration.
	 */
	public function with_frequency( string $frequency ): self {
		$this->frequency = $frequency;
		return $this;
	}

	/**
	 * Loads the JW Player 7 for WP adapter.
	 *
	 * @return $this For chaining configuration.
	 */
	public function with_jw_player_7_for_wp(): self {
		$this->adapter = new Adapters\JW_Player_7_For_WP();
		return $this;
	}

	/**
	 * Schedules the sync cron job if it's not already scheduled.
	 */
	public function maybe_schedule_sync(): void {
		if ( ! wp_next_scheduled( self::CRON_HOOK ) ) {
			wp_schedule_event( time(), $this->frequency, self::CRON_HOOK );
		}
	}

	/**
	 * Syncs videos from the provider.
	 */
	public function sync_videos(): void {
		$last_sync = get_option( self::LAST_SYNC_OPTION, DateTime::createFromFormat( DATE_W3C, '1970-01-01T00:00:00Z' ) );
		$videos    = $this->adapter->get_videos( $last_sync );
		foreach ( $videos as $video ) {
			// Do something with the video.
		}
	}
}

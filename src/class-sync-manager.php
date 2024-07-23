<?php
/**
 * WP_Video_Sync: Sync Manager
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync;

use Alley\WP\WP_Video_Sync\Interfaces\Adapter;
use DateTimeImmutable;
use Error;

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
	 * @var ?Adapter
	 */
	public ?Adapter $adapter;

	/**
	 * A callback to run for each result when the sync runs.
	 *
	 * @var callable
	 */
	public $callback;

	/**
	 * The frequency with which to sync videos. Can be any valid value for wp_schedule_event().
	 *
	 * @var string
	 */
	public string $frequency = 'hourly';

	/**
	 * Factory method that creates a new instance of this class, hooks it, and returns it.
	 *
	 * @return self
	 */
	public static function init(): self {
		$instance = new self();

		add_action( 'admin_init', [ $instance, 'maybe_schedule_sync' ] );
		add_action( self::CRON_HOOK, [ $instance, 'sync_videos' ] );

		return $instance;
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
	 *
	 * @throws Error If unable to parse the last sync as a DateTimeImmutable object.
	 */
	public function sync_videos(): void {
		// If there isn't a valid adapter, bail.
		if ( ! $this->adapter instanceof Adapter ) {
			throw new Error( esc_html__( 'WP Video Sync: Unable to sync videos without a valid adapter.', 'wp-video-sync' ) );
		}

		// If there isn't a valid callback, bail.
		if ( ! is_callable( $this->callback ) ) {
			throw new Error( esc_html__( 'WP Video Sync: Unable to execute provided callback.', 'wp-video-sync' ) );
		}

		// Try to get the option value so we can parse it as a date. If it doesn't exist, default to the Unix epoch.
		$option_value = get_option( self::LAST_SYNC_OPTION, '1970-01-01T00:00:00Z' );
		if ( ! is_string( $option_value ) ) {
			throw new Error( esc_html__( 'WP Video Sync: The value saved to the options table for the last sync time is not a string.', 'wp-video-sync' ) );
		}

		// Try to parse the last sync time into a DateTimeImmutable object and fail if we can't.
		$last_sync = DateTimeImmutable::createFromFormat( DATE_W3C, $option_value );
		if ( ! $last_sync instanceof DateTimeImmutable ) {
			throw new Error( esc_html__( 'WP Video Sync: The last sync time could not be parsed into a DateTimeImmutable object.', 'wp-video-sync' ) );
		}

		// Get a batch of videos and loop over them and process each.
		$videos = $this->adapter->get_videos( $last_sync );
		foreach ( $videos as $video ) {
			call_user_func_array( $this->callback, [ $video ] );
		}

		// Try to update the last sync time to the last modified time of the last video that was processed.
		$next_last_modified = $this->adapter->get_last_modified_date();
		if ( $next_last_modified instanceof DateTimeImmutable ) {
			update_option( self::LAST_SYNC_OPTION, $next_last_modified->format( DATE_W3C ) );
		}
	}

	/**
	 * Allows the adapter to be set. Can be used with an adapter that ships with this plugin or any custom adapter that implements the Adapter interface.
	 *
	 * @param Adapter $adapter The adapter to load.
	 *
	 * @return $this For chaining configuration.
	 */
	public function with_adapter( Adapter $adapter ): self {
		$this->adapter = $adapter;
		return $this;
	}

	/**
	 * Allows a callback to be set that will be called when the sync runs.
	 *
	 * @param callable $callback The callback to run when the sync runs.
	 *
	 * @return $this For chaining configuration.
	 */
	public function with_callback( callable $callback ): self {
		$this->callback = $callback;
		return $this;
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
}

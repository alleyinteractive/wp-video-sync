# WP Video Sync

Contributors: alleyinteractive

Tags: alleyinteractive, wp-video-sync

Stable tag: 0.1.0

Requires at least: 6.0

Tested up to: 6.6

Requires PHP: 8.2

License: GPL v2 or later

[![Testing Suite](https://github.com/alleyinteractive/wp-video-sync/actions/workflows/all-pr-tests.yml/badge.svg)](https://github.com/alleyinteractive/wp-video-sync/actions/workflows/all-pr-tests.yml)

Sync videos from a hosting provider to WordPress.

Runs a scheduled task to sync videos from a supported video hosting provider to WordPress in batches based on the last modified date of the video. Implementers are responsible for installing and configuring a compatible plugin, choosing it as an adapter, and defining the callback that will be run for each video, which will be responsible for performing any post creations or updates in WordPress.

This plugin is a great way to sync videos uploaded to a hosting provider (such as JW Player) to WordPress, such that the video itself remains on the hosting provider, but the video can be displayed in WordPress using a player block or shortcode, appears at its own unique URL, and can be included in search results.

## Installation

You can install the package via Composer:

```bash
composer require alleyinteractive/wp-video-sync
```

## Usage

Activate the plugin in WordPress and use it like so:

```php
use Alley\WP\WP_Video_Sync\Adapters\JW_Player_7_For_WP;
use Alley\WP\WP_Video_Sync\Sync_Manager;
use DateInterval;
use DateTimeImmutable;
use WP_Query;

add_action( 'plugins_loaded', function () {
	$sync_manager = Sync_Manager::init()
		->with_adapter( new JW_Player_7_For_WP() )
		->with_frequency( 'hourly' )
		->with_batch_size( 1000 )
		->with_callback(
			function ( $video ) {
				$existing_video = new WP_Query( [ 'meta_key' => '_jwppp-video-url-1', 'meta_value' => $video->id ] );
				$existing_id    = $existing_video->posts[0]->ID ?? 0;
				$duration       = '';
				try {
					if ( ! empty( $video->metadata->duration ) ) {
						$duration = ( new DateTimeImmutable() )
							->add( new DateInterval( sprintf( 'PT%dS', (int) $video->metadata->duration ) ) )
							->diff( new DateTimeImmutable() )->format( 'H:i:s' );
					}
				} catch ( Exception $e ) {
					$duration = '';
				}
				wp_insert_post(
					[
						'ID'            => $existing_id,
						'post_type'     => 'post',
						'post_status'   => 'publish',
						'post_title'    => $video->metadata->title,
						'post_content'  => $video->metadata->description ?? '',
						'post_date'     => DateTimeImmutable::createFromFormat( DATE_W3C, $video->created )->format( 'Y-m-d H:i:s' ),
						'post_modified' => DateTimeImmutable::createFromFormat( DATE_W3C, $video->last_modified )->format( 'Y-m-d H:i:s' ),
						'meta_input'    => [
							'_jwppp-video-url-1'           => $video->id,
							'_jwppp-cloud-playlist-1'      => 'no',
							'_jwppp-sources-number-1'      => 1,
							'_jwppp-video-title-1'         => $video->metadata->title,
							'_jwppp-video-description-1'   => $video->metadata->description ?? '',
							'_jwppp-activate-media-type-1' => 0,
							'_jwppp-playlist-carousel-1'   => 0,
							'_jwppp-video-duration-1'      => $duration,
							'_jwppp-video-tags-1'          => $video->metadata->tags ?? '',
						],
					]
				);
			}
		);
} );
```

This will configure the plugin to import a batch of 1000 videos every hour from JW Player, sorted by least to most recently updated, starting with the date and time of the last video that was updated. If videos have already been imported (as identified by the postmeta value saved for the unique video ID) they will be updated rather than created. New videos will be created. The example code above uses the `post` post type for this purpose, but the code could easily be adapted to use a custom post type. Additionally, the post content could be set to include a Gutenberg block or a shortcode for a player.

### Supported Adapters

As of now, the plugin only supports JW Player 7 for WordPress (both the free and premium versions). Other adapters may be added in the future.

#### JW Player 7 for WordPress

- Requires the [JW Player 7 for WordPress](https://wordpress.org/plugins/jw-player-7-for-wp/) plugin to be installed, activated, and properly configured with access credentials. Also supports the premium version.
- The video object in the callback is a `stdClass` with the properties described in the `media` object under response code `200` in [the JW Player API documentation for the media list endpoint](https://docs.jwplayer.com/platform/reference/get_v2-sites-site-id-media).

## Releasing the Plugin

New versions of this plugin will be created as releases in GitHub once ready.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

This project is actively maintained by [Alley
Interactive](https://github.com/alleyinteractive). Like what you see? [Come work
with us](https://alley.co/careers/).

- [Alley](https://github.com/Alley)
- [All Contributors](../../contributors)

## License

The GNU General Public License (GPL) license. Please see [License File](LICENSE) for more information.

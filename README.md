# WP Video Sync

Contributors: alleyinteractive

Tags: alleyinteractive, wp-video-sync

Stable tag: 0.0.0

Requires at least: 6.6

Tested up to: 6.6

Requires PHP: 8.2

License: GPL v2 or later

[![Testing Suite](https://github.com/alleyinteractive/wp-video-sync/actions/workflows/all-pr-tests.yml/badge.svg)](https://github.com/alleyinteractive/wp-video-sync/actions/workflows/all-pr-tests.yml)

Sync videos from a hosting provider to WordPress.

## Installation

You can install the package via Composer:

```bash
composer require alleyinteractive/wp-video-sync
```

## Usage

Activate the plugin in WordPress and use it like so:

```php
$video_sync = new Alley\WP\WP_Video_Sync\Sync_Manager()->with_jw_player_7_for_wp();
```

As of now, the plugin only supports JW Player 7 for WordPress (both the free and premium versions). Other adapters may be added in the future.

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

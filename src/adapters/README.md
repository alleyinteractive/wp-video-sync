## Class JW_Player

This class will implement the base `Adapter` interface. Instantiating this class with a `JW_Player_API` object will allow the request to be made to get the latest videos to be synced. This class may be used to work with a theme's custom integration with JW Player.

Example Integration:

```shell
\Alley\WP\WP_Video_Sync\Sync_Manager::init()
	->with_adapter(
		new \Alley\WP\WP_Video_Sync\Adapters\JW_Player(
			[PROPERTY_ID],
			[API_KEY]
		)
	)
	->with_batch_size()
	->with_frequency()
	->with_callback(
		function ( $video ) {
			// Do something with the video.
		}
);
```

## Class JW_Player_7_For_WP

This class is to work with the JW Player 7 for WordPress plugin.

<?php
/**
 * WP Video Sync Tests: JW Player Adapter Test
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Tests\Feature;

use Alley\WP\WP_Video_Sync\Adapters\JW_Player_7_For_WP;
use Alley\WP\WP_Video_Sync\Sync_Manager;
use Alley\WP\WP_Video_Sync\Tests\TestCase;
use DateTimeImmutable;
use WP_Query;

/**
 * A test suite for the JW Player adapter.
 */
class JWPlayerAdapterTest extends TestCase {
	/**
	 * Tests the behavior of the adapter with the JW Player 7 for WP plugin (free and premium).
	 */
	public function test_jw_player_7_for_wp() {
		// Fake the class that's used to make the API call.
		require_once __DIR__ . '/../Mocks/JWPPP_Dashboard_API.php';

		// Fake the API response for the first page of data.
		$this->fake_request( 'https://api.jwplayer.com/v2/sites/example-api-key/media/*' )
			->with_response_code( 200 )
			->with_body( file_get_contents( __DIR__ . '/../Fixtures/jw-player-api-v2-media.json' ) );

		// Create an instance of the adapter.
		$sync_manager = Sync_Manager::init()
			->with_adapter( new JW_Player_7_For_WP() )
			->with_callback( fn ( $video ) => self::factory()->post->create(
				[
					'post_title'    => $video->metadata->title,
					'post_date'     => DateTimeImmutable::createFromFormat( DATE_W3C, $video->created )->format( 'Y-m-d H:i:s' ),
					'post_modified' => DateTimeImmutable::createFromFormat( DATE_W3C, $video->last_modified )->format( 'Y-m-d H:i:s' ),
					'meta_input'    => [
						'jwplayer_id' => $video->id,
					],
				]
			) );

		// Run the sync.
		$sync_manager->sync_videos();

		// Confirm that the sync was successful.
		$video_query = new WP_Query(
			[
				'name'        => 'example-video',
				'post_status' => 'publish',
				'post_type'   => 'post',
			]
		);
		$this->assertEquals( 1, $video_query->post_count );
		$this->assertEquals( 'Example Video', $video_query->posts[0]->post_title );
		$this->assertEquals( '2024-01-01 12:00:00', $video_query->posts[0]->post_date );
		$this->assertEquals( '2024-01-01 12:00:00', $video_query->posts[0]->post_date_gmt );
		$this->assertEquals( '2024-01-01 13:00:00', $video_query->posts[0]->post_modified );
		$this->assertEquals( '2024-01-01 13:00:00', $video_query->posts[0]->post_modified_gmt );
		$this->assertEquals( 'ABCD1234', get_post_meta( $video_query->posts[0]->ID, 'jwplayer_id', true ) );

		// Ensure that the sync time was updated.
		$this->assertEquals( '2024-01-01T13:00:00+00:00', get_option( Sync_Manager::LAST_SYNC_OPTION ) );
	}
}

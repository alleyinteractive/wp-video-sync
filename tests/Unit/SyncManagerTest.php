<?php
/**
 * WP Video Sync Tests: Sync Manager Test
 *
 * @package wp-video-sync
 */

namespace Alley\WP\WP_Video_Sync\Tests\Unit;

use Alley\WP\WP_Video_Sync\Sync_Manager;
use Alley\WP\WP_Video_Sync\Adapters\JW_Player_7_For_WP;
use PHPUnit\Framework\TestCase;

/**
 * Tests configuration for the Sync Manager.
 */
class SyncManagerTest extends TestCase {
	/**
	 * Test that the sync manager can be configured via its helper methods.
	 */
	public function test_configuration() {
		$sync_manager = Sync_Manager::init()
			->with_adapter( new JW_Player_7_For_WP() )
			->with_frequency( 'daily' );
		$this->assertInstanceOf( JW_Player_7_For_WP::class, $sync_manager->adapter );
		$this->assertEquals( 'daily', $sync_manager->frequency );
	}
}

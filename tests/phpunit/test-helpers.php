<?php
/**
 * Tests for {@see \DuckDev\FourNotFour\Utils\Helpers}.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class HelpersTest
 *
 * @group helpers
 */
class HelpersTest extends WP_UnitTestCase {

	/**
	 * Two URLs that should normalise to the same string get the same hash.
	 */
	public function test_url_hash_is_stable_across_trivial_variations(): void {
		$variants = array(
			'/old/page',
			'/Old/Page/',
			'/old/page?utm_source=newsletter',
			'/old/page/?ref=twitter',
		);

		$canonical = Helpers::url_hash( $variants[0] );

		foreach ( $variants as $url ) {
			$this->assertSame( $canonical, Helpers::url_hash( $url ), "Hash should match for $url" );
		}

		// And a different path should produce a different hash.
		$this->assertNotSame( $canonical, Helpers::url_hash( '/other/page' ) );
	}

	/**
	 * The root path stays as `/` rather than being stripped to empty.
	 */
	public function test_normalise_url_preserves_root(): void {
		$this->assertSame( '/', Helpers::normalise_url( '/' ) );
		$this->assertSame( '/', Helpers::normalise_url( '/?utm=x' ) );
	}

	/**
	 * `pack_ip` round-trips through `unpack_ip`.
	 */
	public function test_ip_round_trip_v4_and_v6(): void {
		foreach ( array( '127.0.0.1', '192.168.1.255', '::1', '2001:db8::ff00:42:8329' ) as $ip ) {
			$packed = Helpers::pack_ip( $ip );
			$this->assertNotSame( '', $packed, "Pack failed for $ip" );
			$this->assertSame( strtolower( $ip ), strtolower( Helpers::unpack_ip( $packed ) ) );
		}
	}

	/**
	 * Invalid IPs come back as empty strings, not warnings.
	 */
	public function test_pack_ip_rejects_garbage(): void {
		$this->assertSame( '', Helpers::pack_ip( 'not-an-ip' ) );
		$this->assertSame( '', Helpers::pack_ip( '' ) );
	}

	/**
	 * Obvious bots are detected; common browsers are not.
	 */
	public function test_is_human_heuristic(): void {
		$bots = array(
			'Googlebot/2.1 (+http://www.google.com/bot.html)',
			'Mozilla/5.0 (compatible; bingbot/2.0)',
			'curl/7.64.1',
		);

		foreach ( $bots as $ua ) {
			$this->assertFalse( Helpers::is_human( $ua ), "Should detect $ua as a bot" );
		}

		$browsers = array(
			'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
			'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/123.0 Safari/537.36',
		);

		foreach ( $browsers as $ua ) {
			$this->assertTrue( Helpers::is_human( $ua ), "Should detect $ua as a human" );
		}

		// Empty UA is treated as a bot.
		$this->assertFalse( Helpers::is_human( '' ) );
	}

	/**
	 * Redirect status catalogue is filterable.
	 */
	public function test_redirect_statuses_is_filterable(): void {
		add_filter(
			'404_to_301_redirect_statuses',
			function ( $statuses ) {
				$statuses[308] = '308 Permanent';
				return $statuses;
			}
		);

		$statuses = Helpers::redirect_statuses();
		$this->assertArrayHasKey( 308, $statuses );

		remove_all_filters( '404_to_301_redirect_statuses' );
	}
}

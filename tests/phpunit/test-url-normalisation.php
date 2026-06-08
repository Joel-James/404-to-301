<?php
/**
 * Tests for the URL-normalisation policy and the
 * `404_to_301_normalize_url` filter (#8).
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Database\Database;
use DuckDev\FourNotFour\Models\Redirects;
use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class UrlNormalisationTest
 *
 * @group helpers
 */
class UrlNormalisationTest extends WP_UnitTestCase {

	public function set_up(): void {
		parent::set_up();
		Database::instance();
	}

	public function tear_down(): void {
		remove_all_filters( '404_to_301_normalize_url' );
		parent::tear_down();
	}

	/**
	 * Trailing slash, case, and percent-encoding all collapse onto the
	 * same canonical form.
	 */
	public function test_normalise_url_collapses_common_variants(): void {
		$expected = '/about us';

		foreach (
			array(
				'/about us',
				'/About Us',
				'/about us/',
				'/about%20us',
				'/ABOUT%20US/',
			) as $candidate
		) {
			$this->assertSame(
				$expected,
				Helpers::normalise_url( $candidate ),
				"normalise_url({$candidate}) should equal {$expected}"
			);
		}
	}

	/**
	 * Percent-decoding collapses encoded URLs onto a single hash so a
	 * row stored under the decoded form still matches an encoded
	 * request.
	 */
	public function test_percent_encoded_request_matches_decoded_source(): void {
		$model = Redirects::instance();
		$id    = $model->create(
			array(
				'source'      => '/old product',
				'target_url'  => 'https://example.com/new',
				'target_type' => 'link',
				'match_type'  => 'exact',
				'is_active'   => 1,
			)
		);

		$matched = $model->find_exact( '/old%20product' );
		$this->assertNotNull( $matched );
		$this->assertSame( $id, (int) $matched->id );
	}

	/**
	 * The `404_to_301_normalize_url` filter receives both the
	 * normalised form and the raw input.
	 */
	public function test_normalise_url_filter_receives_both_normalised_and_raw(): void {
		$captured = null;
		add_filter(
			'404_to_301_normalize_url',
			static function ( $normalised, $raw ) use ( &$captured ) {
				$captured = compact( 'normalised', 'raw' );
				return $normalised;
			},
			10,
			2
		);

		Helpers::normalise_url( '/Foo/Bar/' );

		$this->assertIsArray( $captured );
		$this->assertSame( '/foo/bar', $captured['normalised'] );
		$this->assertSame( '/Foo/Bar/', $captured['raw'] );
	}

	/**
	 * A filter that returns a non-string falls back to the helper's
	 * own output — the matcher is never handed a poison value.
	 */
	public function test_normalise_url_filter_falls_back_on_non_string_return(): void {
		add_filter( '404_to_301_normalize_url', static fn () => null );

		$this->assertSame( '/foo', Helpers::normalise_url( '/foo' ) );
	}

	/**
	 * A filter that returns a different string overrides the policy —
	 * eg. case-sensitive matching by keeping the original casing.
	 */
	public function test_normalise_url_filter_can_override_policy(): void {
		add_filter(
			'404_to_301_normalize_url',
			static function ( $normalised, $raw ) {
				// Case-sensitive override: redo just the slash strip
				// and skip lowercasing.
				$path = strtok( trim( $raw ), '?' );
				$path = rawurldecode( (string) $path );
				if ( strlen( $path ) > 1 && '/' === substr( $path, -1 ) ) {
					$path = rtrim( $path, '/' );
				}
				return $path;
			},
			10,
			2
		);

		$this->assertSame( '/About', Helpers::normalise_url( '/About/' ) );
	}

	/**
	 * `url_hash_with_query` (require-mode rows) runs the path through
	 * the same normalisation pipeline.
	 */
	public function test_url_hash_with_query_uses_normalised_path(): void {
		$this->assertSame(
			Helpers::url_hash_with_query( '/Promo?Code=Summer' ),
			Helpers::url_hash_with_query( '/promo?Code=Summer' )
		);

		// But query case is preserved — values can be case-sensitive.
		$this->assertNotSame(
			Helpers::url_hash_with_query( '/promo?Code=Summer' ),
			Helpers::url_hash_with_query( '/promo?Code=summer' )
		);
	}
}

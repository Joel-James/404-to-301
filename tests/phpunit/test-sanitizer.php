<?php
/**
 * Tests for {@see \DuckDev\FourNotFour\Utils\Sanitizer}.
 *
 * @package FourNotFour
 */

declare( strict_types = 1 );

use DuckDev\FourNotFour\Utils\Sanitizer;

/**
 * Class SanitizerTest
 *
 * @group sanitizer
 */
class SanitizerTest extends WP_UnitTestCase {

	public function test_boolean_accepts_truthy_strings(): void {
		foreach ( array( '1', 'true', 'yes', 'on', 1, true ) as $value ) {
			$this->assertTrue( Sanitizer::boolean( $value ) );
		}

		foreach ( array( '0', 'false', 'no', 'off', 0, false, '' ) as $value ) {
			$this->assertFalse( Sanitizer::boolean( $value ) );
		}
	}

	public function test_integer_clamps_within_range(): void {
		$this->assertSame( 0, Sanitizer::integer( -5 ) );
		$this->assertSame( 5, Sanitizer::integer( 5 ) );
		$this->assertSame( 10, Sanitizer::integer( 50, 0, 10 ) );
		$this->assertSame( 1, Sanitizer::integer( 0, 1, 10 ) );
	}

	public function test_enum_falls_back(): void {
		$this->assertSame( 'link', Sanitizer::enum( 'link', array( 'link', 'page' ), 'page' ) );
		$this->assertSame( 'page', Sanitizer::enum( 'bogus', array( 'link', 'page' ), 'page' ) );
	}

	public function test_email_invalid_becomes_empty(): void {
		$this->assertSame( 'me@example.com', Sanitizer::email( 'me@example.com' ) );
		$this->assertSame( '', Sanitizer::email( 'not-an-email' ) );
	}

	public function test_string_list_splits_text_and_dedupes(): void {
		$out = Sanitizer::string_list( "foo\nbar\nfoo\n , baz" );

		$this->assertSame( array( 'foo', 'bar', 'baz' ), $out );
	}

	public function test_string_list_accepts_array(): void {
		$out = Sanitizer::string_list( array( 'a', '  ', 'b', 'a' ) );

		$this->assertSame( array( 'a', 'b' ), $out );
	}
}

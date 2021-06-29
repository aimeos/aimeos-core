<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MW;

use Aimeos\MW\Str;


class StrTest extends \PHPUnit\Framework\TestCase
{
	public function testAfter()
	{
		$this->assertNull( Str::after( 'abc', '' ) );
		$this->assertNull( Str::after( 'abc', 'x' ) );

		$this->assertNull( Str::after( 'abc', null ) );
		$this->assertNull( Str::after( null, 'x' ) );

		$this->assertEquals( 3, Str::after( 123, 2 ) );
		$this->assertEquals( 3, Str::after( 123, 12 ) );

		$this->assertEquals( 'c', Str::after( 'abc', 'b' ) );
		$this->assertEquals( 'c', Str::after( 'abc', 'ab' ) );

		$this->assertEquals( 'こ', Str::after( 'はしこ', 'し' ) );
		$this->assertEquals( 'こ', Str::after( 'はしこ', 'はし' ) );

		$this->assertEquals( '池', Str::after( '他弛池', '弛' ) );
		$this->assertEquals( '池', Str::after( '他弛池', '他弛' ) );

		$this->assertEquals( 'c', Str::after( 'abc', ['b', 'c'] ) );
		$this->assertEquals( '', Str::after( 'abc', ['c', 'b'] ) );
		$this->assertNull( Str::after( 'abc', ['x'] ) );
	}


	public function testBefore()
	{
		$this->assertNull( Str::before( 'abc', '' ) );
		$this->assertNull( Str::before( 'abc', 'x' ) );

		$this->assertNull( Str::before( 'abc', null ) );
		$this->assertNull( Str::before( null, 'x' ) );

		$this->assertEquals( 1, Str::before( 123, 2 ) );
		$this->assertEquals( 1, Str::before( 123, 23 ) );

		$this->assertEquals( 'a', Str::before( 'abc', 'b' ) );
		$this->assertEquals( 'a', Str::before( 'abc', 'bc' ) );

		$this->assertEquals( 'は', Str::before( 'はしこ', 'し' ) );
		$this->assertEquals( 'は', Str::before( 'はしこ', 'しこ' ) );

		$this->assertEquals( '他', Str::before( '他弛池', '弛' ) );
		$this->assertEquals( '他', Str::before( '他弛池', '弛池' ) );

		$this->assertEquals( 'a', Str::before( 'abc', ['b', 'c'] ) );
		$this->assertEquals( 'ab', Str::before( 'abc', ['c', 'b'] ) );
		$this->assertNull( Str::before( 'abc', ['x'] ) );
	}


	public function testEnds()
	{
		$this->assertFalse( Str::ends( 'abc', '' ) );
		$this->assertFalse( Str::ends( 'abc', 'a' ) );
		$this->assertFalse( Str::ends( 'abc', 'ab' ) );

		$this->assertFalse( Str::ends( 'abc', 'x' ) );
		$this->assertFalse( Str::ends( 'abc', ['x', 'y'] ) );

		$this->assertFalse( Str::ends( 'abc', null ) );
		$this->assertFalse( Str::ends( null, 'x' ) );

		$this->assertTrue( Str::ends( 123, 3 ) );
		$this->assertTrue( Str::ends( 123, 23 ) );

		$this->assertTrue( Str::ends( 'abc', ['c', 'x'] ) );
		$this->assertTrue( Str::ends( 'abc', 'bc' ) );
		$this->assertTrue( Str::ends( 'abc', 'c' ) );

		$this->assertTrue( Str::ends( 'はしこ', 'しこ' ) );
		$this->assertTrue( Str::ends( 'はしこ', 'こ' ) );

		$this->assertTrue( Str::ends( '他弛池', '弛池' ) );
		$this->assertTrue( Str::ends( '他弛池', '池' ) );
	}


	public function testHtml()
	{
		$this->assertEquals( '&lt;html&gt;', Str::html( '<html>' ) );
		$this->assertEquals( '123', Str::html( 123 ) );
		$this->assertEquals( '', Str::html( null ) );
	}


	public function testIn()
	{
		$this->assertFalse( Str::in( 'abc', '' ) );
		$this->assertFalse( Str::in( 'abc', 'ax' ) );
		$this->assertFalse( Str::in( 'abc', ['x'] ) );

		$this->assertFalse( Str::in( 'abc', null ) );
		$this->assertFalse( Str::in( null, 'x' ) );

		$this->assertTrue( Str::in( 123, 2 ) );
		$this->assertTrue( Str::in( 123, [1, 3] ) );

		$this->assertTrue( Str::in( 'abc', 'a' ) );
		$this->assertTrue( Str::in( 'abc', ['a', 'c'] ) );
	}


	public function testMethod()
	{
		$this->assertNull( \Aimeos\MW\Str::method( 'upper' ) );

		\Aimeos\MW\Str::method( 'upper', function( $str ) {
			return strtoupper( $str );
		} );

		$this->assertEquals( 'ABC', Str::upper( 'abc' ) );
	}


	public function testSlug()
	{
		$this->assertEquals( 'a_b_c', Str::slug( 'a/b&c', 'en', '_' ) );
		$this->assertEquals( 'Ae-oe-ue', Str::slug( 'Ä/ö&ü', 'de' ) );
		$this->assertEquals( 'a-o-u', Str::slug( 'ä/ö&ü' ) );
		$this->assertEquals( 'a-b-c', Str::slug( 'a/b&c' ) );
		$this->assertEquals( 'a~b', Str::slug( 'a~b' ) );
		$this->assertEquals( '123', Str::slug( 123 ) );
		$this->assertEquals( '', Str::slug( null ) );
	}


	public function testSlugCustom()
	{
		\Aimeos\MW\Str::method( 'slug', function( $str, $lang, $sep ) {
			return strtolower( $str );
		} );

		$this->assertEquals( 'abc', Str::slug( 'ABC' ) );

		\Aimeos\MW\Str::method( 'slug', function( $str, $lang, $sep ) {
			$str = \voku\helper\ASCII::to_ascii( (string) $str, $lang );
			return trim( preg_replace( '/[^A-Za-z0-9]+/', $sep, $str ), $sep );
		} );
	}


	public function testSome()
	{
		$this->assertFalse( Str::some( null, [''] ) );
		$this->assertFalse( Str::some( 'abc', [''] ) );

		$this->assertFalse( Str::some( 'abc', ['ax'] ) );
		$this->assertFalse( Str::some( 'abc', ['x', 'y'] ) );

		$this->assertTrue( Str::some( 123, [2] ) );
		$this->assertTrue( Str::some( 123, [1, 3] ) );

		$this->assertTrue( Str::some( 'abc', ['a'] ) );
		$this->assertTrue( Str::some( 'abc', ['a', 'c'] ) );
		$this->assertTrue( Str::some( 'abc', ['a', 'x'] ) );
	}


	public function testStarts()
	{
		$this->assertFalse( Str::starts( 'abc', '' ) );
		$this->assertFalse( Str::starts( 'abc', 'c' ) );
		$this->assertFalse( Str::starts( 'abc', 'bc' ) );

		$this->assertFalse( Str::starts( 'abc', 'x' ) );
		$this->assertFalse( Str::starts( 'abc', ['x', 'y'] ) );

		$this->assertFalse( Str::starts( 'abc', null ) );
		$this->assertFalse( Str::starts( null, 'c' ) );

		$this->assertTrue( Str::starts( 123, 12 ) );
		$this->assertTrue( Str::starts( 123, 1 ) );

		$this->assertTrue( Str::starts( 'abc', ['a', 'x'] ) );
		$this->assertTrue( Str::starts( 'abc', 'ab' ) );
		$this->assertTrue( Str::starts( 'abc', 'a' ) );

		$this->assertTrue( Str::starts( 'はしこ', 'はし' ) );
		$this->assertTrue( Str::starts( 'はしこ', 'は' ) );

		$this->assertTrue( Str::starts( '他弛池', '他弛' ) );
		$this->assertTrue( Str::starts( '他弛池', '他' ) );
	}


	public function testUid()
	{
		$r = Str::uid();

		$this->assertNotEquals( $r, Str::uid() );
		$this->assertEquals( 20, strlen( $r ) );
	}
}

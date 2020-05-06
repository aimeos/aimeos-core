<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\MW;

use Aimeos\MW\Str;


class PHPTest extends \PHPUnit\Framework\TestCase
{
	public function testAfter()
	{
		$this->assertNull( Str::after( 'abc', '' ) );
		$this->assertNull( Str::after( 'abc', 'x' ) );

		$this->assertEquals( 'c', Str::after( 'abc', 'b' ) );
		$this->assertEquals( 'c', Str::after( 'abc', 'ab' ) );

		$this->assertEquals( 'こ', Str::after( 'はしこ', 'し' ) );
		$this->assertEquals( 'こ', Str::after( 'はしこ', 'はし' ) );

		$this->assertEquals( '池', Str::after( '他弛池', '弛' ) );
		$this->assertEquals( '池', Str::after( '他弛池', '他弛' ) );
	}


	public function testBefore()
	{
		$this->assertNull( Str::before( 'abc', '' ) );
		$this->assertNull( Str::before( 'abc', 'x' ) );

		$this->assertEquals( 'a', Str::before( 'abc', 'b' ) );
		$this->assertEquals( 'a', Str::before( 'abc', 'bc' ) );

		$this->assertEquals( 'は', Str::before( 'はしこ', 'し' ) );
		$this->assertEquals( 'は', Str::before( 'はしこ', 'しこ' ) );

		$this->assertEquals( '他', Str::before( '他弛池', '弛' ) );
		$this->assertEquals( '他', Str::before( '他弛池', '弛池' ) );
	}


	public function testChars()
	{
		$this->assertEquals( 3, strlen( 'abc' ) );
		$this->assertEquals( 3, Str::chars( 'abc' ) );

		$this->assertEquals( 6, strlen( 'äöü' ) );
		$this->assertEquals( 3, Str::chars( 'äöü' ) );

		$this->assertEquals( 9, strlen( 'はしこ' ) );
		$this->assertEquals( 3, Str::chars( 'はしこ' ) );

		$this->assertEquals( 9, strlen( '他弛池' ) );
		$this->assertEquals( 3, Str::chars( '他弛池' ) );
	}


	public function testEnds()
	{
		$this->assertFalse( Str::ends( 'abc', '' ) );
		$this->assertFalse( Str::ends( 'abc', 'a' ) );
		$this->assertFalse( Str::ends( 'abc', 'ab' ) );
		$this->assertFalse( Str::ends( 'abc', 'x' ) );

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
	}


	public function testIn()
	{
		$this->assertFalse( Str::in( 'abc', '' ) );
		$this->assertFalse( Str::in( 'abc', 'ax' ) );
		$this->assertFalse( Str::in( 'abc', ['x'] ) );

		$this->assertTrue( Str::in( 'abc', 'a' ) );
		$this->assertTrue( Str::in( 'abc', ['a', 'c'] ) );
	}


	public function testIs()
	{
		$this->assertTrue( Str::is( '' ) );
		$this->assertTrue( Str::is( 'abc' ) );

		$this->assertFalse( Str::is( 123 ) );
		$this->assertFalse( Str::is( [] ) );
	}


	public function testLimit()
	{
		$this->assertEquals( 'ab', Str::limit( 'abc', 2 ) );
		$this->assertEquals( 'はし', Str::limit( 'はしこ', 2 ) );
		$this->assertEquals( '他弛', Str::limit( '他弛池', 2 ) );
	}


	public function testMatch()
	{
		$this->assertTrue( Str::match( 'abc', '/b/' ) );
		$this->assertFalse( Str::match( 'abc', '/x/' ) );
	}


	public function testSlug()
	{
		$this->assertEquals( 'a_b_c', Str::slug( 'a/b&c', 'en', '_' ) );
		$this->assertEquals( 'ae-oe-ue', Str::slug( 'Ä/ö&ü', 'de' ) );
		$this->assertEquals( 'a-o-u', Str::slug( 'ä/ö&ü' ) );
		$this->assertEquals( 'a-b-c', Str::slug( 'a/b&c' ) );
	}


	public function testSome()
	{
		$this->assertFalse( Str::some( 'abc', [''] ) );
		$this->assertFalse( Str::some( 'abc', ['ax'] ) );
		$this->assertFalse( Str::some( 'abc', ['x', 'y'] ) );

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

		$this->assertTrue( Str::starts( 'abc', 'ab' ) );
		$this->assertTrue( Str::starts( 'abc', 'a' ) );

		$this->assertTrue( Str::starts( 'はしこ', 'はし' ) );
		$this->assertTrue( Str::starts( 'はしこ', 'は' ) );

		$this->assertTrue( Str::starts( '他弛池', '他弛' ) );
		$this->assertTrue( Str::starts( '他弛池', '他' ) );
	}


	public function testStrip()
	{
		$this->assertEquals( 'a  b', Str::strip( 'a <html> b' ) );
		$this->assertEquals( 'a  b <p> c', Str::strip( 'a <html> b <p> c', ['<p>'] ) );
		$this->assertEquals( 'a  b <p onclick=""> c', Str::strip( 'a <html> b <p onclick=""> c', ['<p>'] ) );
	}
}

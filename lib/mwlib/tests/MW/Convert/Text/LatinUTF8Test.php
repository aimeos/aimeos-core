<?php

namespace Aimeos\MW\Convert\Text;


class LatinUTF8Test extends \PHPUnit_Framework_TestCase
{
	public function testTranslate()
	{
		$object = new \Aimeos\MW\Convert\Text\LatinUTF8();

		$this->assertInstanceOf( '\\Aimeos\\MW\\Convert\\Iface', $object );
		$this->assertEquals( 'abc', $object->translate( 'abc' ) );
	}


	public function testReverse()
	{
		$object = new \Aimeos\MW\Convert\Text\LatinUTF8();

		$this->assertInstanceOf( '\\Aimeos\\MW\\Convert\\Iface', $object );
		$this->assertEquals( 'abc', $object->reverse( 'abc' ) );
	}
}

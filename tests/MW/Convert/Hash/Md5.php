<?php

namespace Aimeos\MW\Convert\Hash;


class Md5Test extends \PHPUnit\Framework\TestCase
{
	public function testTranslate()
	{
		$object = new \Aimeos\MW\Convert\Hash\Md5();

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( '900150983cd24fb0d6963f7d28e17f72', $object->translate( 'abc' ) );
	}


	public function testReverse()
	{
		$object = new \Aimeos\MW\Convert\Hash\Md5();

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( 'abc', $object->reverse( 'abc' ) );
	}
}

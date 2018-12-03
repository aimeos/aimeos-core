<?php

namespace Aimeos\MW\Convert\DateTime;


class EnglishISOTest extends \PHPUnit\Framework\TestCase
{
	public function testTranslate()
	{
		$object = new \Aimeos\MW\Convert\DateTime\EnglishISO();

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( '2000-01-02 00:00:00', $object->translate( '01/02/2000' ) );
	}


	public function testReverse()
	{
		$object = new \Aimeos\MW\Convert\DateTime\EnglishISO();

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( '01/02/2000 00:00:00 AM', $object->reverse( '2000-01-02 00:00:00' ) );
	}
}

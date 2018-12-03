<?php

namespace Aimeos\MW\Convert;


class ComposeTest extends \PHPUnit\Framework\TestCase
{
	public function testTranslate()
	{
		$list = array(
			\Aimeos\MW\Convert\Factory::createConverter( 'Text/LatinUTF8' ),
			\Aimeos\MW\Convert\Factory::createConverter( 'DateTime/EnglishISO' ),
		);

		$object = new \Aimeos\MW\Convert\Compose( $list );

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( '2000-01-02 00:00:00', $object->translate( '01/02/2000' ) );
	}


	public function testReverse()
	{
		$list = array(
			\Aimeos\MW\Convert\Factory::createConverter( 'DateTime/EnglishISO' ),
			\Aimeos\MW\Convert\Factory::createConverter( 'Text/LatinUTF8' ),
		);

		$object = new \Aimeos\MW\Convert\Compose( $list );

		$this->assertInstanceOf( \Aimeos\MW\Convert\Iface::class, $object );
		$this->assertEquals( '01/02/2000 00:00:00 AM', $object->reverse( '2000-01-02 00:00:00' ) );
	}
}

<?php


class MW_Convert_DateTime_EnglishISOTest extends PHPUnit_Framework_TestCase
{
	public function testTranslate()
	{
		$object = new MW_Convert_DateTime_EnglishISO();

		$this->assertInstanceOf( 'MW_Convert_Iface', $object );
		$this->assertEquals( '2000-01-02 00:00:00', $object->translate( '01/02/2000' ) );
	}


	public function testReverse()
	{
		$object = new MW_Convert_DateTime_EnglishISO();

		$this->assertInstanceOf( 'MW_Convert_Iface', $object );
		$this->assertEquals( '01/02/2000 00:00:00 AM', $object->reverse( '2000-01-02 00:00:00' ) );
	}
}

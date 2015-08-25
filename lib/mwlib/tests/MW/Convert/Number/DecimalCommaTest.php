<?php


class MW_Convert_Number_DecimalCommaTest extends PHPUnit_Framework_TestCase
{
	public function testTranslate()
	{
		$object = new MW_Convert_Number_DecimalComma();

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( '2.00', $object->translate( '2,00' ) );
	}


	public function testReverse()
	{
		$object = new MW_Convert_Number_DecimalComma();

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( '1,0', $object->reverse( '1.0' ) );
	}
}

<?php


class MW_Convert_Text_LatinUTF8Test extends PHPUnit_Framework_TestCase
{
	public function testTranslate()
	{
		$object = new MW_Convert_Text_LatinUTF8();

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( 'abc', $object->translate( 'abc' ) );
	}


	public function testReverse()
	{
		$object = new MW_Convert_Text_LatinUTF8();

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( 'abc', $object->reverse( 'abc' ) );
	}
}

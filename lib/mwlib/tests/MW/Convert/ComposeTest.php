<?php


class MW_Convert_ComposeTest extends MW_Unittest_Testcase
{
	public function testTranslate()
	{
		$list = array(
			MW_Convert_Factory::createConverter( 'Text/LatinUTF8' ),
			MW_Convert_Factory::createConverter( 'DateTime/EnglishISO' ),
		);

		$object = new MW_Convert_Compose( $list );

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( '2000-01-02 00:00:00', $object->translate( '01/02/2000' ) );
	}


	public function testReverse()
	{
		$list = array(
			MW_Convert_Factory::createConverter( 'DateTime/EnglishISO' ),
			MW_Convert_Factory::createConverter( 'Text/LatinUTF8' ),
		);

		$object = new MW_Convert_Compose( $list );

		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
		$this->assertEquals( '01/02/2000 00:00:00 AM', $object->reverse( '2000-01-02 00:00:00' ) );
	}
}

<?php


class MW_Convert_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateConverter()
	{
		$object = MW_Convert_Factory::createConverter( 'Text/LatinUTF8' );
		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
	}


	public function testCreateConverterCompose()
	{
		$object = MW_Convert_Factory::createConverter( array( 'Text/LatinUTF8', 'DateTime/EnglishISO' ) );
		$this->assertInstanceOf( 'MW_Convert_Interface', $object );
	}


	public function testCreateConverterInvalidName()
	{
		$this->setExpectedException( 'MW_Convert_Exception' );
		MW_Convert_Factory::createConverter( '$' );
	}


	public function testCreateConverterInvalidClass()
	{
		$this->setExpectedException( 'MW_Convert_Exception' );
		MW_Convert_Factory::createConverter( 'Test/Invalid' );
	}


	public function testCreateConverterInvalidInterface()
	{
		$this->setExpectedException( 'MW_Convert_Exception' );
		MW_Convert_Factory::createConverter( 'Test/Test' );
	}
}


class MW_Convert_Test_Test
{
}

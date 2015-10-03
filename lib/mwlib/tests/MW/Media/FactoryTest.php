<?php

/**
 * Test class for MW_Media_Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Media_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testGetImage()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = MW_Media_Factory::get( __DIR__ . $ds .'_testfiles' . $ds . 'image.png' );

		$this->assertInstanceOf( 'MW_Media_Iface', $object );
		$this->assertInstanceOf( 'MW_Media_Image_Iface', $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}


	public function testGetBinary()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = MW_Media_Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'application.txt' );

		$this->assertInstanceOf( 'MW_Media_Iface', $object );
		$this->assertInstanceOf( 'MW_Media_Application_Iface', $object );
		$this->assertEquals( 'text/plain', $object->getMimetype() );
	}


	public function testGetException()
	{
		$this->setExpectedException('MW_Media_Exception');
		MW_Media_Factory::get( null );
	}
}

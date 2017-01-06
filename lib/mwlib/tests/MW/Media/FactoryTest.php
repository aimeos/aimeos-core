<?php

namespace Aimeos\MW\Media;


/**
 * Test class for \Aimeos\MW\Media\Factory.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testGetImage()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds .'_testfiles' . $ds . 'image.png' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Iface', $object );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Image\\Iface', $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}

	public function testGetImageAsResource()
	{
		$ds = DIRECTORY_SEPARATOR;
		$resource = fopen( __DIR__ . $ds .'_testfiles' . $ds . 'image.png', 'rw' );
		$object = \Aimeos\MW\Media\Factory::get( $resource );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Iface', $object );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Image\\Iface', $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}

	public function testGetImageAsString()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( __DIR__ . $ds .'_testfiles' . $ds . 'image.png' );
		$object = \Aimeos\MW\Media\Factory::get( $content );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Iface', $object );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Image\\Iface', $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}


	public function testGetBinary()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'application.txt' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Iface', $object );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Application\\Iface', $object );
		$this->assertEquals( 'text/plain', $object->getMimetype() );
	}
}

<?php

namespace Aimeos\MW\Media;


/**
 * Test class for \Aimeos\MW\Media\Factory.
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testGetImage()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'image.png' );

		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $object );
		$this->assertInstanceOf( \Aimeos\MW\Media\Image\Iface::class, $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}


	public function testGetImageAsResource()
	{
		$ds = DIRECTORY_SEPARATOR;
		if( ( $resource = fopen( __DIR__ . $ds . '_testfiles' . $ds . 'image.png', 'rw' ) ) === false ) {
			throw new \RuntimeException( 'Failed to open ' . __DIR__ . $ds . '_testfiles' . $ds . 'image.png' );
		}

		$object = \Aimeos\MW\Media\Factory::get( $resource );

		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $object );
		$this->assertInstanceOf( \Aimeos\MW\Media\Image\Iface::class, $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}


	public function testGetImageAsString()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( __DIR__ . $ds . '_testfiles' . $ds . 'image.png' );
		$object = \Aimeos\MW\Media\Factory::get( $content );

		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $object );
		$this->assertInstanceOf( \Aimeos\MW\Media\Image\Iface::class, $object );
		$this->assertEquals( 'image/png', $object->getMimetype() );
	}


	public function testGetBinary()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'application.txt' );

		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $object );
		$this->assertInstanceOf( \Aimeos\MW\Media\Application\Iface::class, $object );
		$this->assertEquals( 'text/plain', $object->getMimetype() );
	}


	public function testGetSvg()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'image.svgz' );

		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $object );
		$this->assertInstanceOf( \Aimeos\MW\Media\Image\Iface::class, $object );
		$this->assertEquals( 'image/svg+xml', $object->getMimetype() );
	}
}

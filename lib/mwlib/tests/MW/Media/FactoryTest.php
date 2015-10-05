<?php

namespace Aimeos\MW\Media;


/**
 * Test class for \Aimeos\MW\Media\Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
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


	public function testGetBinary()
	{
		$ds = DIRECTORY_SEPARATOR;
		$object = \Aimeos\MW\Media\Factory::get( __DIR__ . $ds . '_testfiles' . $ds . 'application.txt' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Iface', $object );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Media\\Application\\Iface', $object );
		$this->assertEquals( 'text/plain', $object->getMimetype() );
	}


	public function testGetException()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Media\\Exception');
		\Aimeos\MW\Media\Factory::get( null );
	}
}

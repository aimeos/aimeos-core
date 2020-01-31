<?php

namespace Aimeos\MW\Container;


/**
 * Test class for \Aimeos\MW\Container\Factory.
 *
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testFactory()
	{
		$object = \Aimeos\MW\Container\Factory::getContainer( 'tempfile', 'Zip', 'CSV', [] );
		$this->assertInstanceOf( \Aimeos\MW\Container\Iface::class, $object );
	}

	public function testFactoryFail()
	{
		$this->expectException( \Aimeos\MW\Container\Exception::class );
		\Aimeos\MW\Container\Factory::getContainer( 'tempfile', 'notDefined', 'invalid' );
	}
}

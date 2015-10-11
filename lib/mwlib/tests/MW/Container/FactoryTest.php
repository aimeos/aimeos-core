<?php

namespace Aimeos\MW\Container;


/**
 * Test class for \Aimeos\MW\Container\Factory.
 *
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testFactory()
	{
		$object = \Aimeos\MW\Container\Factory::getContainer( 'tempfile', 'Zip', 'CSV', array() );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Container\\Iface', $object );
	}

	public function testFactoryFail()
	{
		$this->setExpectedException('\\Aimeos\\MW\\Container\\Exception');
		\Aimeos\MW\Container\Factory::getContainer( 'tempfile', 'notDefined', 'invalid' );
	}
}

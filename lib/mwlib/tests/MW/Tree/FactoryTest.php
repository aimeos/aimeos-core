<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Tree;


class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testFactory()
	{
		$dbm = \Aimeos\MW\DB\Factory::createManager( \TestHelperMw::getConfig() );

		$this->setExpectedException('\Aimeos\MW\Tree\Exception');
		\Aimeos\MW\Tree\Factory::createManager( 'DBNestedSet', [], $dbm );
	}


	public function testFactoryFail()
	{
		$this->setExpectedException('\Aimeos\MW\Tree\Exception');
		\Aimeos\MW\Tree\Factory::createManager( 'invalid', [], null );
	}
}

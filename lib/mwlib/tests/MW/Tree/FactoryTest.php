<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Tree;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testFactory()
	{
		$dbm = \Aimeos\MW\DB\Factory::create( \TestHelperMw::getConfig() );

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		\Aimeos\MW\Tree\Factory::create( 'DBNestedSet', [], $dbm );
	}


	public function testFactoryFail()
	{
		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		\Aimeos\MW\Tree\Factory::create( 'invalid', [], null );
	}
}

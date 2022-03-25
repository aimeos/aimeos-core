<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
 */


namespace Aimeos\MW\Tree;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testFactory()
	{
		$conn = \Aimeos\Base\DB\Factory::create( \TestHelper::getConfig() )->get();

		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		\Aimeos\MW\Tree\Factory::create( 'DBNestedSet', [], $conn );
	}


	public function testFactoryFail()
	{
		$this->expectException( \Aimeos\MW\Tree\Exception::class );
		\Aimeos\MW\Tree\Factory::create( 'invalid', [], null );
	}
}

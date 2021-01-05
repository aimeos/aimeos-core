<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Index\Manager;


class MySQLTest extends \PHPUnit\Framework\TestCase
{
	public function testGetSubManager()
	{
		$class = \Aimeos\MShop\Common\Manager\Iface::class;
		$object = new \Aimeos\MShop\Index\Manager\MySQL( \TestHelperMShop::getContext() );

		$this->assertInstanceOf( $class, $object->getSubManager( 'attribute' ) );
		$this->assertInstanceOf( $class, $object->getSubManager( 'catalog' ) );
		$this->assertInstanceOf( $class, $object->getSubManager( 'price' ) );
		$this->assertInstanceOf( $class, $object->getSubManager( 'supplier' ) );
		$this->assertInstanceOf( $class, $object->getSubManager( 'text' ) );
	}
}

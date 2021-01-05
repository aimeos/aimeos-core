<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos;


class MShopTest extends \PHPUnit\Framework\TestCase
{
	public function testCreate()
	{
		$manager = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateSubManager()
	{
		$manager = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute/lists/type' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop::create( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop::create( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop::create( \TestHelperMShop::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute/unknown' );
	}


	public function testCache()
	{
		\Aimeos\MShop::cache( true );

		$context = \TestHelperMShop::getContext();

		$obj1 = \Aimeos\MShop::create( $context, 'attribute' );
		$obj2 = \Aimeos\MShop::create( $context, 'attribute' );

		\Aimeos\MShop::cache( false );
		$this->assertSame( $obj1, $obj2 );
	}
}

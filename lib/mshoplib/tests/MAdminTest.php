<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos;


class MAdminTest extends \PHPUnit\Framework\TestCase
{
	public function testCreate()
	{
		$manager = \Aimeos\MAdmin::create( \TestHelperMShop::getContext(), 'job' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateEmpty()
	{
		$this->expectException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateInvalidName()
	{
		$this->expectException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateNotExisting()
	{
		$this->expectException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), 'unknown' );
	}


	public function testClear()
	{
		\Aimeos\MAdmin::cache( true );

		$context = \TestHelperMShop::getContext();

		$obj1 = \Aimeos\MAdmin::create( $context, 'log' );
		$obj2 = \Aimeos\MAdmin::create( $context, 'log' );

		\Aimeos\MAdmin::cache( false );
		$this->assertSame( $obj1, $obj2 );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
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
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateInvalidName()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateNotExisting()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin::create( \TestHelperMShop::getContext(), 'job/unknown' );
	}


	public function testClear()
	{
		$cache = \Aimeos\MAdmin::cache( true );

		$context = \TestHelperMShop::getContext();

		$controller1 = \Aimeos\MAdmin::create( $context, 'log' );
		\Aimeos\MAdmin::clear();
		$controller2 = \Aimeos\MAdmin::create( $context, 'log' );

		\Aimeos\MAdmin::cache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\MAdmin::cache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MAdmin::create( $context, 'log' );
		\Aimeos\MAdmin::clear( (string) $context );
		$managerA2 = \Aimeos\MAdmin::create( $context, 'log' );

		\Aimeos\MAdmin::cache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\MAdmin::cache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MAdmin::create( $context, 'log' );
		$managerB1 = \Aimeos\MAdmin::create( $context, 'job' );

		\Aimeos\MAdmin::clear( (string) $context, 'log' );

		$managerA2 = \Aimeos\MAdmin::create( $context, 'log' );
		$managerB2 = \Aimeos\MAdmin::create( $context, 'job' );

		\Aimeos\MAdmin::cache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertSame( $managerB1, $managerB2 );
	}

}
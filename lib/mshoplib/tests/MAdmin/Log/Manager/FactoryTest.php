<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MAdmin\Log\Manager;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MAdmin\Log\Manager\Factory::create( \TestHelperMShop::getContext() );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerName()
	{
		$manager = \Aimeos\MAdmin\Log\Manager\Factory::create( \TestHelperMShop::getContext(), 'Standard' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->expectException( \Aimeos\MAdmin\Log\Exception::class );
		\Aimeos\MAdmin\Log\Manager\Factory::create( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		\Aimeos\MAdmin\Log\Manager\Factory::create( \TestHelperMShop::getContext(), 'unknown' );
	}

}

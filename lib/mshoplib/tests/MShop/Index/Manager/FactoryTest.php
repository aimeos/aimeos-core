<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Factory.
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MShop\Index\Manager\Factory::create( \TestHelperMShop::getContext() );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerName()
	{
		$manager = \Aimeos\MShop\Index\Manager\Factory::create( \TestHelperMShop::getContext(), 'Standard' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop\Index\Manager\Factory::create( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		\Aimeos\MShop\Index\Manager\Factory::create( \TestHelperMShop::getContext(), 'unknown' );
	}
}

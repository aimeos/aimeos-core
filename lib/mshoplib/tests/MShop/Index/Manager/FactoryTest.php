<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MShop\Index\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerName()
	{
		$manager = \Aimeos\MShop\Index\Manager\Factory::createManager( \TestHelperMShop::getContext(), 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Index\Manager\Factory::createManager( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Index\Manager\Factory::createManager( \TestHelperMShop::getContext(), 'unknown' );
	}
}
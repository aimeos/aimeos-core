<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Product\Manager;


/**
 * Test class for \Aimeos\MShop\Product\Manager\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerName()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext(), 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Product\\Exception' );
		\Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext(), '%^&' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext(), 'unknown' );
	}
}
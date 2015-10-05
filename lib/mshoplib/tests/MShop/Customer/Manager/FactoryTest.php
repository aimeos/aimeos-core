<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Customer\Manager;


/**
 * Test class for \Aimeos\MShop\Customer\Manager\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( \TestHelper::getContext() );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );

	}


	public function testCreateManagerName()
	{
		$manager = \Aimeos\MShop\Customer\Manager\Factory::createManager( \TestHelper::getContext(), 'Standard' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Customer\\Exception' );
		\Aimeos\MShop\Customer\Manager\Factory::createManager( \TestHelper::getContext(), '%$@' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Customer\Manager\Factory::createManager( \TestHelper::getContext(), 'unknown' );
	}

}
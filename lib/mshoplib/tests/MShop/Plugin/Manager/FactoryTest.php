<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Plugin_Manager_Factory.
 */
class MShop_Plugin_Manager_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $manager );
	}


	public function testCreateManagerName()
	{
		$manager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Plugin_Exception' );
		MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext(), '%$@' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext(), 'unknown' );
	}
}
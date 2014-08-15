<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_ServicesAvailableTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_service;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'ServicesAvailable' );
		$this->_plugin->setStatus( 1 );

		$orderBaseManager = MShop_Order_Manager_Factory::createManager( $context )->getSubManager( 'base' );
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );

		$this->_order = $orderBaseManager->createItem();
		$this->_service = $orderBaseServiceManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_orderManager );
		unset( $this->_plugin );
		unset( $this->_service );
		unset( $this->_order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable(TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_SERVICE not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable(TestHelper::getContext(), $this->_plugin);
		$this->assertTrue( $object->update( $this->_order, 'check.after' ) );
	}

	public function testUpdateEmptyConfig()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable(TestHelper::getContext(), $this->_plugin );
		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

		$this->_order->setService( $this->_service, 'payment' );
		$this->_order->setService( $this->_service, 'delivery' );
		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

	}

	public function testUpdateNoServices()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

		$this->_plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

		$this->_plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE );
	}

	public function testUpdateWithServices()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_order->setService( $this->_service, 'payment' );
		$this->_order->setService( $this->_service, 'delivery' );

		$this->_plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

		$this->_plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE ) );

		$this->_plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_SERVICE );
	}
}
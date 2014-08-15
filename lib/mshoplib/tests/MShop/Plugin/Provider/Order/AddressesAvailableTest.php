<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_AddressesAvailableTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_address;


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
		$this->_plugin->setProvider( 'AddressesAvailable' );
		$this->_plugin->setStatus( 1 );

		$orderBaseManager = MShop_Order_Manager_Factory::createManager( $context )->getSubManager( 'base' );
		$orderBaseAddressManager = $orderBaseManager->getSubManager('address');

		$this->_order = $orderBaseManager->createItem();

		$this->_address = $orderBaseAddressManager->createItem();
		$this->_address->setLastName('Available');
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_plugin );
		unset( $this->_order );
		unset( $this->_address );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_ADDRESS not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin);
		$this->assertTrue( $object->update( $this->_order, 'check.after' ) );
	}

	public function testEmptyConfig()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );
		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );
	}

	public function testUpdateAddressesNotAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => false
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => true
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}

	public function testUpdateAddressesAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );
		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => true
		) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT => false
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}
}
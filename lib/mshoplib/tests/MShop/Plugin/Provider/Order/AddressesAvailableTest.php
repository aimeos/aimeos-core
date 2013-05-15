<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @version $Id$
 */

class MShop_Plugin_Provider_Order_AddressesAvailableTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_address;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_AddressesAvailableTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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

		$this->_orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$this->_order = $orderBaseManager->createItem();

		$this->_plugin->setConfig( array() );

		$orderBaseAddressManager = $orderBaseManager->getSubManager('address');

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
		unset( $this->_orderManager );
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
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after' ) );
	}

	public function testEmptyConfig()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );
		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );
	}

	public function testUpdateAddressesNotAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => false
		) );

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => true
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}

	public function testUpdateAddressesAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable(TestHelper::getContext(), $this->_plugin );

		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );
		$this->_order->setAddress( $this->_address, MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => null
		) );

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => true
		) );

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS ) );

		$this->_plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING => false
		) );

		$this->setExpectedException('MShop_Plugin_Provider_Exception');
		$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_ADDRESS );
	}
}
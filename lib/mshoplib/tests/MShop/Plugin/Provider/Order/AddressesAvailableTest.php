<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_AddressesAvailableTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $address;


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
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'AddressesAvailable' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = MShop_Order_Manager_Factory::createManager( $context )->getSubManager( 'base' );
		$orderBaseAddressManager = $orderBaseManager->getSubManager( 'address' );

		$this->order = $orderBaseManager->createItem();

		$this->address = $orderBaseAddressManager->createItem();
		$this->address->setLastName( 'Available' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->plugin );
		unset( $this->order );
		unset( $this->address );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Base::PARTS_ADDRESS not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable( TestHelper::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after' ) );
	}

	public function testEmptyConfig()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable( TestHelper::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );

		$this->order->setAddress( $this->address, MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT );
		$this->order->setAddress( $this->address, MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY );
		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );
	}

	public function testUpdateAddressesNotAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable( TestHelper::getContext(), $this->plugin );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => false
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => true
		) );

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS );
	}

	public function testUpdateAddressesAvailable()
	{
		$object = new MShop_Plugin_Provider_Order_AddressesAvailable( TestHelper::getContext(), $this->plugin );

		$this->order->setAddress( $this->address, MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT );
		$this->order->setAddress( $this->address, MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => null,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => true,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => true
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY => false,
				MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT => false
		) );

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_ADDRESS );
	}
}
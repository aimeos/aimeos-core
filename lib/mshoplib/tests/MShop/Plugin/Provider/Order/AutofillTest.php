<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_AutofillTest extends PHPUnit_Framework_TestCase
{
	private $_plugin;
	private $_orderManager;
	private $_order;


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
		$this->_plugin->setProvider( 'Autofill' );
		$this->_plugin->setStatus( 1 );

		$this->_orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->_orderManager->getSubManager('base');

		$this->_order = $orderBaseManager->createItem();
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
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdateNone()
	{
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( array(), $this->_order->getServices() );

	}


	public function testUpdateOrderNoItem()
	{
		$context = TestHelper::getContext();
		$context->setUserId( '' );
		$this->_plugin->setConfig( array( 'autofill.useorder' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( array(), $this->_order->getServices() );
	}


	public function testUpdateOrderNone()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Factory::createManager( $context, 'customer' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'No customer item for code UTC001" found' );
		}

		$context->setUserId( $customer->getId() );
		$this->_plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '0'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( array(), $this->_order->getServices() );
	}


	public function testUpdateOrderAddress()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Factory::createManager( $context, 'customer' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'No customer item for code UTC001" found' );
		}


		$orderStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseAddressStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Address_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems' ) )->getMock();

		$item1 = $orderBaseAddressStub->createItem();
		$item1->setType( MShop_Order_Item_Base_Address_Abstract::TYPE_DELIVERY );
		$item2 = $orderBaseAddressStub->createItem();
		$item2->setType( MShop_Order_Item_Base_Address_Abstract::TYPE_PAYMENT );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseAddressStub ) );
		$orderBaseAddressStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_PluginAutofill', $orderStub );
		$context->getConfig()->set( 'classes/order/manager/name', 'PluginAutofill' );


		$context->setUserId( $customer->getId() );
		$this->_plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '1',
			'autofill.orderservice' => '0'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->_order->getAddresses() ) );
		$this->assertEquals( array(), $this->_order->getServices() );
	}


	public function testUpdateOrderService()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Factory::createManager( $context, 'customer' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new Exception( 'No customer item for code UTC001" found' );
		}


		$orderStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseServiceStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Service_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems' ) )->getMock();

		$item1 = $orderBaseServiceStub->createItem();
		$item1->setType( MShop_Order_Item_Base_Service_Abstract::TYPE_DELIVERY );
		$item2 = $orderBaseServiceStub->createItem();
		$item2->setType( MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseServiceStub ) );
		$orderBaseServiceStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_PluginAutofill', $orderStub );
		$context->getConfig()->set( 'classes/order/manager/name', 'PluginAutofill' );


		$context->setUserId( $customer->getId() );
		$this->_plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '1'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->_order->getServices() ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
	}


	public function testUpdateDelivery()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_DELIVERY;
		$this->_plugin->setConfig( array( 'autofill.delivery' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
	}


	public function testUpdateDeliveryCode()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_DELIVERY;
		$this->_plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'unitcode' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
		$this->assertEquals( 'unitcode', $this->_order->getService( $type )->getCode() );
	}


	public function testUpdateDeliveryCodeNotExists()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_DELIVERY;
		$this->_plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'xyz' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
	}


	public function testUpdatePayment()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT;
		$this->_plugin->setConfig( array( 'autofill.payment' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
	}


	public function testUpdatePaymentCode()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT;
		$this->_plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'unitpaymentcode' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
		$this->assertEquals( 'unitpaymentcode', $this->_order->getService( $type )->getCode() );
	}


	public function testUpdatePaymentCodeNotExists()
	{
		$type = MShop_Order_Item_Base_Service_Abstract::TYPE_PAYMENT;
		$this->_plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'xyz' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->_order->getAddresses() );
		$this->assertEquals( 1, count( $this->_order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->_order->getService( $type ) );
	}
}
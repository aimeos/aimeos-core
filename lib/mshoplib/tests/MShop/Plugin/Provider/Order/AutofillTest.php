<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_AutofillTest extends PHPUnit_Framework_TestCase
{
	private $plugin;
	private $orderManager;
	private $order;


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
		$this->plugin->setProvider( 'Autofill' );
		$this->plugin->setStatus( 1 );

		$this->orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );

		$this->order = $orderBaseManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->orderManager );
		unset( $this->plugin );
		unset( $this->order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( array(), $this->order->getServices() );

	}


	public function testUpdateOrderNoItem()
	{
		$context = TestHelper::getContext();
		$context->setUserId( '' );
		$this->plugin->setConfig( array( 'autofill.useorder' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( array(), $this->order->getServices() );
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
		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '0'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( array(), $this->order->getServices() );
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
		$item1->setType( MShop_Order_Item_Base_Address_Base::TYPE_DELIVERY );
		$item2 = $orderBaseAddressStub->createItem();
		$item2->setType( MShop_Order_Item_Base_Address_Base::TYPE_PAYMENT );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseAddressStub ) );
		$orderBaseAddressStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_PluginAutofill', $orderStub );
		$context->getConfig()->set( 'classes/order/manager/name', 'PluginAutofill' );


		$context->setUserId( $customer->getId() );
		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '1',
			'autofill.orderservice' => '0'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->order->getAddresses() ) );
		$this->assertEquals( array(), $this->order->getServices() );
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
		$item1->setType( MShop_Order_Item_Base_Service_Base::TYPE_DELIVERY );
		$item1->setCode( 'unitcode' );

		$item2 = $orderBaseServiceStub->createItem();
		$item2->setType( MShop_Order_Item_Base_Service_Base::TYPE_PAYMENT );
		$item2->setCode( 'unitpaymentcode' );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseServiceStub ) );
		$orderBaseServiceStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_PluginAutofill', $orderStub );
		$context->getConfig()->set( 'classes/order/manager/name', 'PluginAutofill' );


		$context->setUserId( $customer->getId() );
		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '1'
		) );
		$object = new MShop_Plugin_Provider_Order_Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->order->getServices() ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
	}


	public function testUpdateDelivery()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
	}


	public function testUpdateDeliveryCode()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'unitcode' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
		$this->assertEquals( 'unitcode', $this->order->getService( $type )->getCode() );
	}


	public function testUpdateDeliveryCodeNotExists()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'xyz' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
	}


	public function testUpdatePayment()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
	}


	public function testUpdatePaymentCode()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'unitpaymentcode' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
		$this->assertEquals( 'unitpaymentcode', $this->order->getService( $type )->getCode() );
	}


	public function testUpdatePaymentCodeNotExists()
	{
		$type = MShop_Order_Item_Base_Service_Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'xyz' ) );
		$object = new MShop_Plugin_Provider_Order_Autofill( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( array(), $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Service_Interface', $this->order->getService( $type ) );
	}
}
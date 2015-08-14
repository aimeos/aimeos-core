<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_ServicesUpdateTest
	extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Factory::createManager( $context, 'plugin' );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'ServicesUpdate' );
		$this->_plugin->setStatus( 1 );

		$orderBaseManager = MShop_Factory::createManager( $context, 'order/base' );
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
		unset( $this->_plugin );
		unset( $this->_order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdate()
	{
		$context = TestHelper::getContext();
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( $context, $this->_plugin );

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$localeManager = MShop_Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( 'MShop_Order_Item_Base_Default' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )->setMethods( array( 'getProducts' ) )->getMock();

		$serviceStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems', 'getProvider' ) )->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_PluginServicesUpdate', $serviceStub );
		$context->getConfig()->set( 'classes/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );

		$serviceItemDelivery = new MShop_Service_Item_Default( array( 'type' => 'delivery' ) );
		$serviceItemPayment = new MShop_Service_Item_Default( array( 'type' => 'payment' ) );


		$providerStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceStub->createItem() ) )
			->setMethods( array( 'isAvailable' ) )->getMock();

		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( array( $orderProduct ) ) );

		$serviceStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( 1 => $serviceItemDelivery, 2 => $serviceItemPayment ) ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->will( $this->returnValue( $providerStub ) );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->will( $this->returnValue( true ) );


		$this->assertTrue( $object->update( $orderStub, 'addProduct.after' ) );
		$this->assertNotSame( $serviceDelivery, $orderStub->getService( 'delivery' ) );
		$this->assertNotSame( $servicePayment, $orderStub->getService( 'payment' ) );
	}


	public function testUpdateNotAvailable()
	{
		$context = TestHelper::getContext();
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( $context, $this->_plugin );

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$localeManager = MShop_Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( 'MShop_Order_Item_Base_Default' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )->setMethods( array( 'getProducts' ) )->getMock();

		$serviceStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems', 'getProvider' ) )->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_PluginServicesUpdate', $serviceStub );
		$context->getConfig()->set( 'classes/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );

		$serviceItemDelivery = new MShop_Service_Item_Default( array( 'type' => 'delivery' ) );
		$serviceItemPayment = new MShop_Service_Item_Default( array( 'type' => 'payment' ) );


		$providerStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceStub->createItem() ) )
			->setMethods( array( 'isAvailable' ) )->getMock();

		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( array( $orderProduct ) ) );

		$serviceStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( 1 => $serviceItemDelivery, 2 => $serviceItemPayment ) ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->will( $this->returnValue( $providerStub ) );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->will( $this->returnValue( false ) );


		$this->assertTrue( $object->update( $orderStub, 'addProduct.after' ) );
		$this->assertEquals( array(), $orderStub->getServices() );
	}


	public function testUpdateServicesGone()
	{
		$context = TestHelper::getContext();
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( $context, $this->_plugin );

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$localeManager = MShop_Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( -1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( -2 );


		$orderStub = $this->getMockBuilder( 'MShop_Order_Item_Base_Default' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )
			->setMethods( array( 'getProducts' ) )->getMock();


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );


		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( array( $orderProduct ) ) );


		$this->assertTrue( $object->update( $orderStub, 'addAddress.after' ) );
		$this->assertEquals( array(), $orderStub->getServices() );
	}


	public function testUpdateNoProducts()
	{
		$context = TestHelper::getContext();
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( $context, $this->_plugin );

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$priceItem->setCosts( '5.00' );

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setPrice( $priceItem );
		$serviceDelivery->setId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setPrice( $priceItem );
		$servicePayment->setId( 2 );

		$this->_order->setService( $serviceDelivery, 'delivery' );
		$this->_order->setService( $servicePayment, 'payment' );


		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertEquals( '0.00', $this->_order->getService( 'delivery' )->getPrice()->getCosts() );
		$this->assertEquals( '0.00', $this->_order->getService( 'payment' )->getPrice()->getCosts() );
	}
}
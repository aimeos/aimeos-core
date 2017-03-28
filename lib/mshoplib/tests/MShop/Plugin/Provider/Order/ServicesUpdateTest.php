<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ServicesUpdateTest
	extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Factory::createManager( $context, 'plugin' );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ServicesUpdate' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners
	}


	protected function tearDown()
	{
		unset( $this->plugin );
		unset( $this->order );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdate()
	{
		$context = \TestHelperMShop::getContext();
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $context, $this->plugin );

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Item\\Base\\Standard' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )->setMethods( array( 'getProducts' ) )->getMock();

		$serviceStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Manager\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems', 'getProvider' ) )->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Service\\Manager\\PluginServicesUpdate', $serviceStub );
		$context->getConfig()->set( 'mshop/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );

		$serviceItemDelivery = new \Aimeos\MShop\Service\Item\Standard( array( 'type' => 'delivery' ) );
		$serviceItemPayment = new \Aimeos\MShop\Service\Item\Standard( array( 'type' => 'payment' ) );


		$providerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Delivery\\Manual' )
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
		$context = \TestHelperMShop::getContext();
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $context, $this->plugin );

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Item\\Base\\Standard' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )->setMethods( array( 'getProducts' ) )->getMock();

		$serviceStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Manager\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems', 'getProvider' ) )->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Service\\Manager\\PluginServicesUpdate', $serviceStub );
		$context->getConfig()->set( 'mshop/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );

		$serviceItemDelivery = new \Aimeos\MShop\Service\Item\Standard( array( 'type' => 'delivery' ) );
		$serviceItemPayment = new \Aimeos\MShop\Service\Item\Standard( array( 'type' => 'payment' ) );


		$providerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Delivery\\Manual' )
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
		$this->assertEquals( [], $orderStub->getServices() );
	}


	public function testUpdateServicesGone()
	{
		$context = \TestHelperMShop::getContext();
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $context, $this->plugin );

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$localeManager = \Aimeos\MShop\Factory::createManager( $context, 'locale' );
		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$localeItem = $localeManager->createItem();
		$orderProduct = $orderBaseProductManager->createItem();

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setServiceId( -1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setServiceId( -2 );


		$orderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Item\\Base\\Standard' )
			->setConstructorArgs( array( $priceItem, $localeItem ) )
			->setMethods( array( 'getProducts' ) )->getMock();


		$orderStub->setService( $serviceDelivery, 'delivery' );
		$orderStub->setService( $servicePayment, 'payment' );


		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( array( $orderProduct ) ) );


		$this->assertTrue( $object->update( $orderStub, 'addAddress.after' ) );
		$this->assertEquals( [], $orderStub->getServices() );
	}


	public function testUpdateNoProducts()
	{
		$context = \TestHelperMShop::getContext();
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $context, $this->plugin );

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$orderBaseServiceManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/service' );

		$priceItem = $priceManager->createItem();
		$priceItem->setCosts( '5.00' );

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setPrice( $priceItem );
		$serviceDelivery->setId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setPrice( $priceItem );
		$servicePayment->setId( 2 );

		$this->order->setService( $serviceDelivery, 'delivery' );
		$this->order->setService( $servicePayment, 'payment' );


		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( '0.00', $this->order->getService( 'delivery' )->getPrice()->getCosts() );
		$this->assertEquals( '0.00', $this->order->getService( 'payment' )->getPrice()->getCosts() );
	}
}
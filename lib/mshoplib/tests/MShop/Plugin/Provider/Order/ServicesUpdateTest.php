<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ServicesUpdateTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $order;
	private $plugin;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->order, $this->plugin, $this->context );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$localeManager = \Aimeos\MShop::create( $this->context, 'locale' );
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Base\Standard::class )
			->setConstructorArgs( [$priceItem, $localeItem] )->setMethods( ['getProducts'] )->getMock();

		$serviceStub = $this->getMockBuilder( \Aimeos\MShop\Service\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )->setMethods( ['search', 'getProvider'] )->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\Aimeos\MShop\Service\Manager\PluginServicesUpdate', $serviceStub );
		$this->context->getConfig()->set( 'mshop/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );

		$serviceItemDelivery = $serviceManager->create()->setType( 'delivery' );
		$serviceItemPayment = $serviceManager->create()->setType( 'payment' );


		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->setMethods( ['isAvailable'] )->getMock();

		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( map( [$orderProduct] ) ) );

		$serviceStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [1 => $serviceItemDelivery, 2 => $serviceItemPayment] ) ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->will( $this->returnValue( $providerStub ) );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->will( $this->returnValue( true ) );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addProduct.after' ) );
		$this->assertNotSame( $serviceDelivery, $orderStub->getService( 'delivery' ) );
		$this->assertNotSame( $servicePayment, $orderStub->getService( 'payment' ) );
	}


	public function testUpdateNotAvailable()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$localeManager = \Aimeos\MShop::create( $this->context, 'locale' );
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Base\Standard::class )
			->setConstructorArgs( [$priceItem, $localeItem] )->setMethods( ['getProducts'] )->getMock();

		$serviceStub = $this->getMockBuilder( \Aimeos\MShop\Service\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )->setMethods( ['search', 'getProvider'] )->getMock();

		\Aimeos\MShop\Service\Manager\Factory::injectManager( '\Aimeos\MShop\Service\Manager\PluginServicesUpdate', $serviceStub );
		$this->context->getConfig()->set( 'mshop/service/manager/name', 'PluginServicesUpdate' );


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );

		$serviceItemDelivery = $serviceManager->create()->setType( 'delivery' );
		$serviceItemPayment = $serviceManager->create()->setType( 'payment' );


		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->setMethods( ['isAvailable'] )->getMock();

		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( map( [$orderProduct] ) ) );

		$serviceStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [1 => $serviceItemDelivery, 2 => $serviceItemPayment] ) ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->will( $this->returnValue( $providerStub ) );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->will( $this->returnValue( false ) );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addProduct.after' ) );
		$this->assertEquals( ['delivery' => [], 'payment' => []], $orderStub->getServices()->toArray() );
	}


	public function testUpdateServicesGone()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$localeManager = \Aimeos\MShop::create( $this->context, 'locale' );
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( -1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( -2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Base\Standard::class )
			->setConstructorArgs( [$priceItem, $localeItem] )
			->setMethods( ['getProducts'] )->getMock();


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );


		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->will( $this->returnValue( map( [$orderProduct] ) ) );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addAddress.after' ) );
		$this->assertEquals( ['delivery' => [], 'payment' => []], $orderStub->getServices()->toArray() );
	}


	public function testUpdateNoProducts()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );

		$priceItem = $priceManager->create()->setCosts( '5.00' );

		$serviceDelivery = $orderBaseServiceManager->create()->setPrice( $priceItem )->setId( 1 );
		$servicePayment = $orderBaseServiceManager->create()->setPrice( $priceItem )->setId( 2 );

		$this->order->addService( $serviceDelivery, 'delivery' );
		$this->order->addService( $servicePayment, 'payment' );


		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );

		foreach( $this->order->getServices() as $list )
		{
			foreach( $list as $item ) {
				$this->assertEquals( '0.00', $item->getPrice()->getCosts() );
			}
		}
	}
}

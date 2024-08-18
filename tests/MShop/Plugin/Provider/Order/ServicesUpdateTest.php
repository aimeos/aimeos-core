<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2024
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
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $this->context, 'order' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesUpdate( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
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
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Standard::class )
			->setConstructorArgs( ['order.', ['.price' => $priceItem, '.locale' => $localeItem]] )
			->onlyMethods( ['getProducts'] )->getMock();

		$serviceStub = $this->getMockBuilder( \Aimeos\MShop\Service\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )->onlyMethods( ['search', 'getProvider'] )->getMock();

		\Aimeos\MShop::inject( \Aimeos\MShop\Service\Manager\Standard::class, $serviceStub );


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );

		$serviceItemDelivery = $serviceManager->create()->setType( 'delivery' );
		$serviceItemPayment = $serviceManager->create()->setType( 'payment' );


		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->onlyMethods( ['isAvailable'] )->getMock();

		$orderStub->expects( $this->any() )->method( 'getProducts' )
			->willReturn( map( [$orderProduct] ) );

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [1 => $serviceItemDelivery, 2 => $serviceItemPayment] ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->willReturn( $providerStub );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->willReturn( true );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addProduct.after' ) );
		$this->assertNotSame( $serviceDelivery, $orderStub->getService( 'delivery' ) );
		$this->assertNotSame( $servicePayment, $orderStub->getService( 'payment' ) );
	}


	public function testUpdateNotAvailable()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$localeManager = \Aimeos\MShop::create( $this->context, 'locale' );
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( 1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( 2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Standard::class )
			->setConstructorArgs( ['order.', ['.price' => $priceItem, '.locale' => $localeItem]] )
			->onlyMethods( ['getProducts'] )->getMock();

		$serviceStub = $this->getMockBuilder( \Aimeos\MShop\Service\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )->onlyMethods( ['search', 'getProvider'] )->getMock();

		\Aimeos\MShop::inject( \Aimeos\MShop\Service\Manager\Standard::class, $serviceStub );


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );

		$serviceItemDelivery = $serviceManager->create()->setType( 'delivery' );
		$serviceItemPayment = $serviceManager->create()->setType( 'payment' );


		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->onlyMethods( ['isAvailable'] )->getMock();

		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->willReturn( map( [$orderProduct] ) );

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [1 => $serviceItemDelivery, 2 => $serviceItemPayment] ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->willReturn( $providerStub );

		$providerStub->expects( $this->exactly( 2 ) )->method( 'isAvailable' )
			->willReturn( false );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addProduct.after' ) );
		$this->assertEquals( ['delivery' => [], 'payment' => []], $orderStub->getServices()->toArray() );
	}


	public function testUpdateServicesGone()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$localeManager = \Aimeos\MShop::create( $this->context, 'locale' );
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/product' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/service' );

		$priceItem = $priceManager->create();
		$localeItem = $localeManager->create();
		$orderProduct = $orderBaseProductManager->create();

		$serviceDelivery = $orderBaseServiceManager->create()->setServiceId( -1 );
		$servicePayment = $orderBaseServiceManager->create()->setServiceId( -2 );


		$orderStub = $this->getMockBuilder( \Aimeos\MShop\Order\Item\Standard::class )
			->setConstructorArgs( ['order.', ['.price' => $priceItem, '.locale' => $localeItem]] )
			->onlyMethods( ['getProducts'] )->getMock();


		$orderStub->addService( $serviceDelivery, 'delivery' );
		$orderStub->addService( $servicePayment, 'payment' );


		$orderStub->expects( $this->once() )->method( 'getProducts' )
			->willReturn( map( [$orderProduct] ) );


		$this->assertEquals( null, $this->object->update( $orderStub, 'addAddress.after' ) );
		$this->assertEquals( ['delivery' => [], 'payment' => []], $orderStub->getServices()->toArray() );
	}


	public function testUpdateNoProducts()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/service' );

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

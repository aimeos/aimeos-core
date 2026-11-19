<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ServicesAvailableTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $order;
	private $plugin;
	private $service;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();
		$this->service = \Aimeos\MShop::create( $this->context, 'order/service' )->create();
		$this->order = \Aimeos\MShop::create( $this->context, 'order' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesAvailable( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->plugin, $this->service, $this->order, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'payment' => '1',
			'delivery' => '0',
			'available' => '1',
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 3, count( $result ) );
		$this->assertEquals( null, $result['payment'] );
		$this->assertEquals( null, $result['delivery'] );
		$this->assertEquals( null, $result['available'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 3, count( $list ) );
		$this->assertArrayHasKey( 'payment', $list );
		$this->assertArrayHasKey( 'delivery', $list );
		$this->assertArrayHasKey( 'available', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateNone()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateEmptyConfig()
	{
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	public function testUpdateNoServices()
	{
		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
	}


	public function testUpdateEmptyServices()
	{
		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->order->deleteService( 'payment' );
		$this->order->deleteService( 'delivery' );

		$this->plugin->setConfig( array(
			'delivery' => false,
			'payment' => false
		) );
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
	}


	public function testUpdateWithServices()
	{
		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
	}


	public function testUpdateAvailableTrue()
	{
		$this->plugin->setConfig( ['available' => '1'] );

		$order = $this->orderStub( 2 );
		$serviceStub = $this->serviceStub();

		$serviceItem = \Aimeos\MShop::create( $this->context, 'service' )->create()->setType( 'payment' );

		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->onlyMethods( ['isAvailable'] )->getMock();

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [2 => $serviceItem] ) );

		$serviceStub->expects( $this->once() )->method( 'getProvider' )
			->willReturn( $providerStub );

		$providerStub->expects( $this->once() )->method( 'isAvailable' )
			->willReturn( true );

		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $order, 'check.after', $part ) );
		$this->assertEquals( 1, count( $order->getService( 'payment' ) ) );
	}


	public function testUpdateAvailableFalse()
	{
		$this->plugin->setConfig( ['available' => '1'] );

		$order = $this->orderStub( 2 );
		$serviceStub = $this->serviceStub();

		$serviceItem = \Aimeos\MShop::create( $this->context, 'service' )->create()->setType( 'payment' );

		$providerStub = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Delivery\Standard::class )
			->setConstructorArgs( [$this->context, $serviceStub->create()] )
			->onlyMethods( ['isAvailable'] )->getMock();

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [2 => $serviceItem] ) );

		$serviceStub->expects( $this->once() )->method( 'getProvider' )
			->willReturn( $providerStub );

		$providerStub->expects( $this->once() )->method( 'isAvailable' )
			->willReturn( false );

		try
		{
			$this->object->update( $order, 'check.after', ['order/service'] );
			$this->fail( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( [], $order->getService( 'payment' ) );
			$this->assertEquals( ['service' => ['payment' => 'available.notallowed']], $e->getErrorCodes() );
		}
	}


	public function testUpdateAvailableDefault()
	{
		$this->plugin->setConfig( [] );

		$order = $this->orderStub( 2 );
		$serviceStub = $this->serviceStub();

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [] ) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $order, 'check.after', ['order/service'] );
	}


	public function testUpdateAvailableGone()
	{
		$this->plugin->setConfig( ['available' => '1'] );

		$order = $this->orderStub( 2 );
		$serviceStub = $this->serviceStub();

		$serviceStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [] ) );

		$serviceStub->expects( $this->never() )->method( 'getProvider' );

		try
		{
			$this->object->update( $order, 'check.after', ['order/service'] );
			$this->fail( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( [], $order->getService( 'payment' ) );
			$this->assertEquals( ['service' => ['payment' => 'available.notallowed']], $e->getErrorCodes() );
		}
	}


	public function testUpdateAvailableDisabled()
	{
		$this->plugin->setConfig( ['available' => ''] );

		$order = $this->orderStub( 2 );
		$serviceStub = $this->serviceStub();

		$serviceStub->expects( $this->never() )->method( 'search' );
		$serviceStub->expects( $this->never() )->method( 'getProvider' );

		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $order, 'check.after', $part ) );
		$this->assertEquals( 1, count( $order->getService( 'payment' ) ) );
	}


	/**
	 * Creates an order stub with a payment service referencing the given service ID
	 *
	 * @param int $serviceId Service ID the order service references
	 * @return \Aimeos\MShop\Order\Item\Iface Order stub with event listeners disabled
	 */
	protected function orderStub( int $serviceId ) : \Aimeos\MShop\Order\Item\Iface
	{
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create();
		$localeItem = \Aimeos\MShop::create( $this->context, 'locale' )->create();

		$order = new \Aimeos\MShop\Order\Item\Standard( 'order.', ['.price' => $priceItem, '.locale' => $localeItem] );
		$order->off();

		$this->service->setServiceId( $serviceId );
		$order->addService( $this->service, 'payment' );

		return $order;
	}


	/**
	 * Injects and returns a service manager mock with search() and getProvider() stubbed
	 *
	 * @return \PHPUnit\Framework\MockObject\MockObject Service manager mock
	 */
	protected function serviceStub()
	{
		$serviceStub = $this->getMockBuilder( \Aimeos\MShop\Service\Manager\Standard::class )
			->setConstructorArgs( [$this->context] )->onlyMethods( ['search', 'getProvider'] )->getMock();

		\Aimeos\MShop::inject( \Aimeos\MShop\Service\Manager\Standard::class, $serviceStub );

		return $serviceStub;
	}
}

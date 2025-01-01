<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2025
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class AutofillTest extends \PHPUnit\Framework\TestCase
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

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object, $this->plugin, $this->order, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'address' => '1',
			'delivery' => '0',
			'deliverycode' => 'ship',
			'payment' => '1',
			'paymentcode' => 'pay',
			'useorder' => '0',
			'orderaddress' => '1',
			'orderservice' => '0',
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 8, count( $result ) );
		$this->assertEquals( null, $result['address'] );
		$this->assertEquals( null, $result['delivery'] );
		$this->assertEquals( null, $result['deliverycode'] );
		$this->assertEquals( null, $result['payment'] );
		$this->assertEquals( null, $result['paymentcode'] );
		$this->assertEquals( null, $result['useorder'] );
		$this->assertEquals( null, $result['orderaddress'] );
		$this->assertEquals( null, $result['orderservice'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 8, count( $list ) );
		$this->assertArrayHasKey( 'address', $list );
		$this->assertArrayHasKey( 'delivery', $list );
		$this->assertArrayHasKey( 'deliverycode', $list );
		$this->assertArrayHasKey( 'payment', $list );
		$this->assertArrayHasKey( 'paymentcode', $list );
		$this->assertArrayHasKey( 'useorder', $list );
		$this->assertArrayHasKey( 'orderaddress', $list );
		$this->assertArrayHasKey( 'orderservice', $list );

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
		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( [], $this->order->getServices()->toArray() );
	}


	public function testUpdateOrderNoItem()
	{
		$this->plugin->setConfig( array( 'useorder' => '1' ) );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( [], $this->order->getServices()->toArray() );
	}


	public function testUpdateOrderNone()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUser( $manager->find( 'test@example.com' ) );

		$this->plugin->setConfig( array(
			'useorder' => '1',
			'orderaddress' => '0',
			'orderservice' => '0'
		) );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( [], $this->order->getServices()->toArray() );
	}


	public function testUpdateOrderAddress()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUser( $manager->find( 'test@example.com' ) );

		$this->plugin->setConfig( array(
			'useorder' => '1',
			'orderaddress' => '1',
			'orderservice' => '0'
		) );

		$result = $this->object->update( $this->order, 'addProduct.after' );

		$this->assertEquals( null, $result );
		$this->assertEquals( 2, count( $this->order->getAddresses() ) );
		$this->assertEquals( [], $this->order->getServices()->toArray() );
	}


	public function testUpdateOrderService()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUser( $manager->find( 'test@example.com' ) );

		$this->plugin->setConfig( array(
			'useorder' => '1',
			'orderaddress' => '0',
			'orderservice' => '1'
		) );

		$result = $this->object->update( $this->order, 'addProduct.after' );

		$this->assertEquals( null, $result );
		$this->assertEquals( 2, count( $this->order->getServices() ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );

		foreach( $this->order->getService( \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT ) as $item ) {
			$this->assertEquals( 4, count( $item->getAttributeItems() ) );
		}
	}


	public function testUpdateAddress()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'customer' );
		$this->context->setUser( $manager->find( 'test@example.com' ) );

		$this->plugin->setConfig( ['address' => '1'] );
		$type = \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT;

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getServices()->toArray() );
		$this->assertEquals( 1, count( $this->order->getAddresses() ) );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Address\Iface::class, $this->order->getAddress( $type, 0 ) );
	}


	public function testUpdateDelivery()
	{
		$this->plugin->setConfig( ['delivery' => '1'] );
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_DELIVERY;

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertCount( 0, $item->getAttributeItems() );
			$this->assertNull( $item->getId() );
		}
	}


	public function testUpdateDeliveryCode()
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( ['delivery' => '1', 'deliverycode' => 'unitdeliverycode'] );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertEquals( 'unitdeliverycode', $item->getCode() );
			$this->assertNull( $item->getId() );
		}
	}


	public function testUpdateDeliveryCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( ['delivery' => '1', 'deliverycode' => 'xyz'] );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertNull( $item->getId() );
		}
	}


	public function testUpdatePayment()
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( ['payment' => '1'] );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertCount( 0, $item->getAttributeItems() );
			$this->assertNull( $item->getId() );
		}
	}


	public function testUpdatePaymentCode()
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( ['payment' => '1', 'paymentcode' => 'unitpaymentcode'] );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertEquals( 'unitpaymentcode', $item->getCode() );
			$this->assertNull( $item->getId() );
		}
	}


	public function testUpdatePaymentCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( ['payment' => '1', 'paymentcode' => 'xyz'] );

		$this->assertEquals( null, $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses()->toArray() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $item );
			$this->assertNull( $item->getId() );
		}
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class FreeProductTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $orderProduct;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$address = \Aimeos\MShop::create( $context, 'order/address' )->create()->setEmail( 'test@example.com' );

		$manager = \Aimeos\MShop::create( $context, 'order/product' );
		$this->orderProduct = $manager->create()->setProductCode( 'ABCD' );
		$this->orderProduct = $this->orderProduct->setPrice( $this->orderProduct->getPrice()->setValue( '100.00' ) );

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners
		$this->order->addAddress( $address, 'payment' )->addProduct( $this->orderProduct );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\FreeProduct( $context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->plugin, $this->orderProduct, $this->order, $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'productcode' => 'ABCD',
			'count' => 1,
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['productcode'] );
		$this->assertEquals( null, $result['count'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'productcode', $list );
		$this->assertArrayHasKey( 'count', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateInvalid()
	{
		$this->expectException( \UnexpectedValueException::class );
		$this->object->update( $this->order, 'addProduct.after' );
	}


	public function testUpdateWrongProductCode()
	{
		$this->orderProduct->setProductCode( 'xyz' );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateNoAddress()
	{
		$this->order->deleteAddress( 'payment' );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateCountExceeded()
	{
		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdate()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 5] );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getRebate() );
	}


	public function testUpdateFirstOrder()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );

		$this->object->update( $this->order, 'addProduct.after', $this->orderProduct );

		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getRebate() );
	}


	public function testUpdatePaymentAddressOnly()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 3] );

		$this->object->update( $this->order, 'addProduct.after', $this->orderProduct );

		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateQuantityExceeded()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );

		$this->object->update( $this->order, 'addProduct.after', $this->orderProduct->setQuantity( 2 ) );

		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateBasketExceeded()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );

		$product = ( clone $this->orderProduct )->setPrice( clone $this->orderProduct->getPrice() );
		$this->order->addProduct( $product, 1 );

		$this->object->update( $this->order, 'addProduct.after', $product );

		$this->assertEquals( '100.00', $product->getPrice()->getValue() );
	}


	public function testUpdateBasketRevoke()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );
		$this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );

		$product = ( clone $this->orderProduct )->setPrice( clone $this->orderProduct->getPrice() );
		$this->order->addProduct( $product, 1 );

		$this->object->update( $this->order, 'addProduct.after', $product );

		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $product->getPrice()->getValue() );
	}


	public function testUpdateRestore()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );

		$price = $this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );
		$this->object->update( $this->order, 'addProduct.after', $this->orderProduct->setPrice( $price )->setQuantity( 2 ) );

		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getRebate() );
	}


	public function testUpdateUnfinished()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		$price = $this->orderProduct->getPrice()->getValue();
		$item = null;

		try
		{
			$item = $this->unfinished();
			$this->object->update( $this->order, 'addProduct.after', $this->orderProduct );
			$price = $this->orderProduct->getPrice()->getValue();
		}
		finally
		{
			$item ? \Aimeos\MShop::create( \TestHelper::context(), 'order' )->delete( $item ) : null;
		}

		$this->assertEquals( '100.00', $price );
	}


	public function testUpdateUnfinishedExpired()
	{
		$context = \TestHelper::context();
		$context->config()->set( 'mshop/coupon/provider/unfinished-hours', -1 );

		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\FreeProduct( $context, $this->plugin );
		$price = $this->orderProduct->getPrice()->getValue();
		$item = null;

		try
		{
			$item = $this->unfinished();
			$object->update( $this->order, 'addProduct.after', $this->orderProduct );
			$price = $this->orderProduct->getPrice()->getValue();
		}
		finally
		{
			$item ? \Aimeos\MShop::create( $context, 'order' )->delete( $item ) : null;
		}

		$this->assertEquals( '0.00', $price );
	}


	public function testUpdateAlreadyFree()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );
		$this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );

		$this->object->update( $this->order, 'addProduct.after', $this->orderProduct );

		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getRebate() );
	}


	public function testUpdateOrder()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );
		$this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );

		$this->assertNull( $this->object->update( $this->order, 'setOrder.before' ) );
		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateOrderNotFree()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 0] );

		$this->assertNull( $this->object->update( $this->order, 'setOrder.before' ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateOrderNotLowered()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 5] );

		$this->assertNull( $this->object->update( $this->order, 'setOrder.before' ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateOrderUnfinished()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		$this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );
		$item = null;

		try
		{
			$item = $this->unfinished();

			$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
			$this->object->update( $this->order, 'setOrder.before' );
		}
		finally
		{
			$item ? \Aimeos\MShop::create( \TestHelper::context(), 'order' )->delete( $item ) : null;
			$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
			$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getRebate() );
		}
	}


	public function testUpdateOrderNoAddress()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 1] );
		$this->orderProduct->getPrice()->setValue( '0.00' )->setRebate( '100.00' );
		$this->order->deleteAddress( 'payment' );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'setOrder.before' );
	}


	protected function unfinished() : \Aimeos\MShop\Order\Item\Iface
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );

		$address = \Aimeos\MShop::create( $context, 'order/address' )->create()->setEmail( 'unittest-free@example.com' );
		$product = \Aimeos\MShop::create( $context, 'order/product' )->create()->setProductCode( 'ABCD' );

		current( $this->order->getAddress( 'payment' ) )->setEmail( 'unittest-free@example.com' );

		$item = $manager->create()->off()->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED )
			->addAddress( $address, 'payment' )->addProduct( $product );

		return $manager->save( $item );
	}
}

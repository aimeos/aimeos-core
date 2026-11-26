<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2026
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class CouponTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Coupon( $context, $plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->order, $this->object );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$this->order->addProduct( $this->getOrderProduct() )->addCoupon( 'OPQR' );
		$this->assertEquals( null, $this->object->update( $this->order, 'test' ) );
		$this->assertEquals( ['OPQR'], $this->order->getCoupons()->keys()->all() );
	}


	public function testUpdateNotAvailable()
	{
		$this->order->addCoupon( 'OPQR' ); // minimum basket value of 9.00 EUR isn't reached

		try
		{
			$this->object->update( $this->order, 'test' );
			$this->fail( 'Plugin provider exception expected' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( 409, $e->getCode() );
			$this->assertStringContainsString( 'OPQR', $e->getMessage() );
		}

		$this->assertEquals( [], $this->order->getCoupons()->all() );
	}


	public function testUpdateProviderException()
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product' );
		$filter = $manager->filter()->add( ['order.product.prodcode' => 'CNE', 'order.product.price' => '36.00'] );

		$this->order->addProduct( $manager->search( $filter )->first( new \Exception( 'No order product' ) ) );
		$this->order->addCoupon( 'STUV' )->addCoupon( '90AB' ); // unpaid voucher, percent rebate

		try
		{
			$this->object->update( $this->order, 'test' );
			$this->fail( 'Plugin provider exception expected' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( 409, $e->getCode() );
		}

		$this->assertEquals( ['90AB'], $this->order->getCoupons()->keys()->all() );
		$this->assertEquals( 1, count( $this->order->getCoupons()->get( '90AB' ) ) );
	}


	public function testUpdateUnfinished()
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		$codeItem = $codeManager->find( 'OPQR' );
		$codeItem = $codeManager->save( ( clone $codeItem )->setId( null )->setCode( 'unittest-count' )->setCount( 1 ) );

		$item = $manager->create()->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED );
		$item = $manager->save( $item->setCoupon( 'unittest-count', [] ) );

		try
		{
			$this->assertEquals( null, $this->object->update( $item, 'test' ) );

			$this->order->addProduct( $this->getOrderProduct() )->addCoupon( 'unittest-count' );
			$this->object->update( $this->order, 'test' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( 409, $e->getCode() );
		}
		finally
		{
			$manager->delete( $item );
			$codeManager->delete( $codeItem );
		}

		$this->assertTrue( isset( $e ) );
		$this->assertEquals( [], $this->order->getCoupons()->all() );
	}


	public function testUpdateUnfinishedExpired()
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );

		$codeItem = $codeManager->find( 'OPQR' );
		$codeItem = $codeManager->save( ( clone $codeItem )->setId( null )->setCode( 'unittest-count' )->setCount( 1 ) );

		$item = $manager->create()->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED );
		$item = $manager->save( $item->setCoupon( 'unittest-count', [] ) );

		try
		{
			$context->config()->set( 'mshop/coupon/provider/unfinished-hours', -1 );
			$object = new \Aimeos\MShop\Plugin\Provider\Order\Coupon( $context, \Aimeos\MShop::create( $context, 'plugin' )->create() );

			$this->order->addProduct( $this->getOrderProduct() )->addCoupon( 'unittest-count' );
			$this->assertEquals( null, $object->update( $this->order, 'test' ) );
			$this->assertEquals( ['unittest-count'], $this->order->getCoupons()->keys()->all() );
		}
		finally
		{
			$manager->delete( $item );
			$codeManager->delete( $codeItem );
		}
	}


	/**
	 * Returns an order product with a value above the minimum basket value of the test coupons
	 *
	 * @return \Aimeos\MShop\Order\Item\Product\Iface Order product item
	 */
	protected function getOrderProduct() : \Aimeos\MShop\Order\Item\Product\Iface
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'order/product' );
		$filter = $manager->filter()->add( ['order.product.prodcode' => 'CNE', 'order.product.price' => '36.00'] );

		return $manager->search( $filter )->first( new \Exception( 'No order product' ) );
	}
}

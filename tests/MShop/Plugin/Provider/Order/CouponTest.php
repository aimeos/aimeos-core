<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2025
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
		$this->order->addCoupon( 'OPQR' );
		$this->assertEquals( null, $this->object->update( $this->order, 'test' ) );
	}


	public function testUpdateUnfinished()
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );
		$codeItem = $item = null;

		try
		{
			$item = $this->unfinished( $codeItem, $item );
			$this->assertEquals( null, $this->object->update( $item->setCoupon( 'unittest-count', [] ), 'test' ) );

			$this->order->addCoupon( 'unittest-count' );
			$this->object->update( $this->order, 'test' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Exception::class, $e );
		}
		finally
		{
			$item ? $manager->delete( $item ) : null;
			$codeItem ? $codeManager->delete( $codeItem ) : null;
		}

		$this->assertTrue( isset( $e ) );
		$this->assertEquals( [], $this->order->getCoupons()->all() );
	}


	public function testUpdateUnfinishedExpired()
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );
		$codeItem = $item = null;

		try
		{
			$item = $this->unfinished( $codeItem, $item );

			$context->config()->set( 'mshop/coupon/provider/unfinished-hours', -1 );
			$object = new \Aimeos\MShop\Plugin\Provider\Order\Coupon( $context, \Aimeos\MShop::create( $context, 'plugin' )->create() );

			$this->order->addCoupon( 'unittest-count' );
			$this->assertEquals( null, $object->update( $this->order, 'test' ) );
			$this->assertEquals( ['unittest-count'], $this->order->getCoupons()->keys()->all() );
		}
		finally
		{
			$item ? $manager->delete( $item ) : null;
			$codeItem ? $codeManager->delete( $codeItem ) : null;
		}
	}


	protected function unfinished( &$codeItem, &$item )
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );
		$couponManager = \Aimeos\MShop::create( $context, 'order/coupon' );

		$codeItem = $codeManager->find( 'OPQR' );
		$codeItem = $codeManager->save( ( clone $codeItem )->setId( null )->setCode( 'unittest-count' )->setCount( 1 ) );

		$item = $manager->save( $manager->create()->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED ) );
		$couponManager->save( $couponManager->create()->setParentId( $item->getId() )->setCode( 'unittest-count' ) );

		return $item;
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2022
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
		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->create()->off(); // remove event listeners

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


	public function testUpdateInvalidObject()
	{
		$this->expectException( \UnexpectedValueException::class );
		$this->object->update( new TestPublisher(), 'test' );
	}


	public function testUpdateUnfinished()
	{
		$context = \TestHelper::context();
		$base = $codeItem = $item = null;

		try
		{
			$base = $this->unfinished( $codeItem, $item );
			$this->assertEquals( null, $this->object->update( $base->setCoupon( 'unittest-count', [] ), 'test' ) );

			$this->order->addCoupon( 'unittest-count' );
			$this->object->update( $this->order, 'test' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Exception::class, $e );
		}
		finally
		{
			$this->cleanup( $base, $codeItem, $item );
		}

		$this->assertTrue( isset( $e ) );
		$this->assertEquals( [], $this->order->getCoupons()->all() );
	}


	public function testUpdateUnfinishedExpired()
	{
		$context = \TestHelper::context();
		$base = $codeItem = $item = null;

		try
		{
			$base = $this->unfinished( $codeItem, $item );

			$context->config()->set( 'mshop/coupon/provider/unfinished-hours', -1 );
			$object = new \Aimeos\MShop\Plugin\Provider\Order\Coupon( $context, \Aimeos\MShop::create( $context, 'plugin' )->create() );

			$this->order->addCoupon( 'unittest-count' );
			$this->assertEquals( null, $object->update( $this->order, 'test' ) );
			$this->assertEquals( ['unittest-count'], $this->order->getCoupons()->keys()->all() );
		}
		finally
		{
			$this->cleanup( $base, $codeItem, $item );
		}
	}


	protected function cleanup( $base, $codeItem, $item )
	{
		$context = \TestHelper::context();

		$item ? \Aimeos\MShop::create( $context, 'order' )->delete( $item->getId() ) : null;
		$base ? \Aimeos\MShop::create( $context, 'order/base' )->delete( $base->getId() ) : null;
		$codeItem ? \Aimeos\MShop::create( $context, 'coupon/code' )->delete( $codeItem->getId() ) : null;
	}


	protected function unfinished( &$codeItem, &$item )
	{
		$context = \TestHelper::context();
		$manager = \Aimeos\MShop::create( $context, 'order' );
		$baseManager = \Aimeos\MShop::create( $context, 'order/base' );
		$codeManager = \Aimeos\MShop::create( $context, 'coupon/code' );
		$couponManager = \Aimeos\MShop::create( $context, 'order/base/coupon' );

		$codeItem = $codeManager->find( 'OPQR' );
		$codeItem = $codeManager->save( ( clone $codeItem )->setId( null )->setCode( 'unittest-count' )->setCount( 1 ) );

		$base = $baseManager->save( $baseManager->create()->off()->setComment( 'unittest' ) );
		$couponManager->save( $couponManager->create()->setBaseId( $base->getId() )->setCode( 'unittest-count' ) );
		$item = $manager->save( $manager->create()->setBaseId( $base->getId() )
			->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED ) );

		return $base;
	}
}


class TestPublisher implements \Aimeos\MW\Observer\Publisher\Iface
{
	use \Aimeos\MW\Observer\Publisher\Traits;
}

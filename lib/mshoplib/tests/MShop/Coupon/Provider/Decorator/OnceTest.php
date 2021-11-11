<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class OnceTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $orderBase;
	private $couponItem;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->couponItem = \Aimeos\MShop::create( $this->context, 'coupon' )->create();

		$orderBaseManager = \Aimeos\MShop::create( $this->context, 'order/base' );
		$search = $orderBaseManager->filter()->add( ['order.base.price' => '2400.00'] );
		$basket = $orderBaseManager->search( $search )->first( new \RuntimeException( 'No order base item found' ) );

		$this->orderBase = $orderBaseManager->load( $basket->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS );
	}


	protected function tearDown() : void
	{
		unset( $this->context, $this->orderBase, $this->couponItem );
	}


	public function testIsAvailable()
	{
		$provider = new \Aimeos\MShop\Coupon\Provider\None( $this->context, $this->couponItem, 'ABCD' );
		$object = new \Aimeos\MShop\Coupon\Provider\Decorator\Once( $provider, $this->context, $this->couponItem, 'ABCD' );
		$object->setObject( $object );

		$this->assertTrue( $object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableExisting()
	{
		$provider = new \Aimeos\MShop\Coupon\Provider\None( $this->context, $this->couponItem, 'OPQR' );
		$object = new \Aimeos\MShop\Coupon\Provider\Decorator\Once( $provider, $this->context, $this->couponItem, 'OPQR' );
		$object->setObject( $object );

		$this->assertFalse( $object->isAvailable( $this->orderBase ) );
	}
}

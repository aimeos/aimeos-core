<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class OnceTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $orderBase;
	private $couponItem;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->couponItem = \Aimeos\MShop\Factory::createManager( $this->context, 'coupon' )->createItem();

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );
		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '4800.00' ) );
		$baskets = $orderBaseManager->searchItems( $search );

		if( ( $basket = reset( $baskets ) ) === false ) {
			throw new \RuntimeException( 'No order base with price "4800.00" found' );
		}

		$this->orderBase = $orderBaseManager->load( $basket->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->orderBase, $this->couponItem );
	}


	public function testIsAvailable()
	{
		$provider = new \Aimeos\MShop\Coupon\Provider\Example( $this->context, $this->couponItem, 'ABCD' );
		$object = new \Aimeos\MShop\Coupon\Provider\Decorator\Once( $provider, $this->context, $this->couponItem, 'ABCD');
		$object->setObject( $object );

		$this->assertTrue( $object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableExisting()
	{
		$provider = new \Aimeos\MShop\Coupon\Provider\Example( $this->context, $this->couponItem, 'OPQR' );
		$object = new \Aimeos\MShop\Coupon\Provider\Decorator\Once( $provider, $this->context, $this->couponItem, 'OPQR');
		$object->setObject( $object );

		$this->assertFalse( $object->isAvailable( $this->orderBase ) );
	}
}

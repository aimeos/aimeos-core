<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2025
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class CategoryTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $order;
	private $couponItem;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->couponItem = \Aimeos\MShop::create( $this->context, 'coupon' )
			->create()->setConfig( ['category.code' => 'cafe', 'category.only' => '1'] );

		$provider = new \Aimeos\MShop\Coupon\Provider\None( $this->context, $this->couponItem, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Category( $provider, $this->context, $this->couponItem, 'abcd' );
		$this->object->setObject( $this->object );

		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' );
		$orderProduct = \Aimeos\MShop::create( $this->context, 'order/product' )->create()->setQuantity( 2 );
		$orderPrice = $orderProduct->copyFrom( $product )->getPrice();
		$orderPrice->setValue( '18.00' )->setCosts( '1.50' );

		$this->order = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();
		$this->order->addProduct( $orderProduct );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->order );
		unset( $this->couponItem );
	}


	public function testCalcPrice()
	{
		$price = $this->object->calcPrice( $this->order );
		$this->assertEquals( 39.0, $price->getValue() + $price->getCosts() );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'category.code', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['category.code' => 'test'];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['category.code'] );
		$this->assertNull( $result['category.only'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['category.code'] );
		$this->assertNull( $result['category.only'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}


	public function testIsAvailableWithProduct()
	{
		$this->couponItem->setConfig( array( 'category.code' => 'cafe' ) );

		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}


	public function testIsAvailableWithoutProduct()
	{
		$this->couponItem->setConfig( array( 'category.code' => 'tea' ) );

		$this->assertFalse( $this->object->isAvailable( $this->order ) );
	}


	public function testIsAvailableMultiple()
	{
		$this->couponItem->setConfig( array( 'category.code' => 'cafe,tea' ) );

		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}
}

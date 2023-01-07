<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2017-2023
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class RequiredTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;
	private $couponItem;


	protected function setUp() : void
	{
		$orderProducts = [];
		$context = \TestHelper::context();
		$this->couponItem = \Aimeos\MShop::create( $context, 'coupon' )->create();

		$provider = new \Aimeos\MShop\Coupon\Provider\None( $context, $this->couponItem, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Required( $provider, $context, $this->couponItem, 'abcd' );
		$this->object->setObject( $this->object );

		$orderProductManager = \Aimeos\MShop::create( $context, 'order/product' );

		$productManager = \Aimeos\MShop::create( $context, 'product' );
		$search = $productManager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', ['CNC', 'CNE'] ) );
		$products = $productManager->search( $search )->toArray();

		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$price = $priceManager->create();

		foreach( $products as $product ) {
			$orderProducts[$product->getCode()] = $orderProductManager->create()->copyFrom( $product );
		}

		$orderProducts['CNC']->setPrice( clone $price->setValue( 321 ) );
		$orderProducts['CNE']->setPrice( clone $price->setValue( 123 ) );

		$this->orderBase = new \Aimeos\MShop\Order\Item\Standard( $priceManager->create(), $context->locale() );
		$this->orderBase->addProduct( $orderProducts['CNC'] );
		$this->orderBase->addProduct( $orderProducts['CNE'] );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
		unset( $this->couponItem );
	}


	public function testCalcPrice()
	{
		$this->couponItem->setConfig( ['required.productcode' => 'CNC'] );

		$price = $this->object->calcPrice( $this->orderBase );
		$this->assertEquals( 444, $price->getValue() + $price->getCosts() );
	}


	public function testCalcPriceOnly()
	{
		$this->couponItem->setConfig( ['required.productcode' => 'CNC', 'required.only' => 1] );

		$price = $this->object->calcPrice( $this->orderBase );
		$this->assertEquals( 321, $price->getValue() + $price->getCosts() );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'required.productcode', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['required.productcode' => 'test'];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['required.productcode'] );
		$this->assertNull( $result['required.only'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['required.productcode'] );
		$this->assertNull( $result['required.only'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableTrue()
	{
		$this->couponItem->setConfig( array( 'required.productcode' => 'CNC,CNE,ABCD' ) );

		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableFalse()
	{
		$this->couponItem->setConfig( array( 'required.productcode' => 'ABCD' ) );

		$this->assertFalse( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableWithProduct()
	{
		$this->couponItem->setConfig( array( 'required.productcode' => 'CNC' ) );

		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableWithoutProduct()
	{
		$this->couponItem->setConfig( array( 'required.productcode' => 'ABCD' ) );

		$this->assertFalse( $this->object->isAvailable( $this->orderBase ) );
	}

}

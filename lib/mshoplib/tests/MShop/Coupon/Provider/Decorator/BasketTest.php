<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class BasketTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;
	private $couponItem;


	protected function setUp() : void
	{
		$orderProducts = [];
		$context = \TestHelperMShop::getContext();

		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::create( $context );
		$this->couponItem = $couponManager->create();

		$provider = new \Aimeos\MShop\Coupon\Provider\None( $context, $this->couponItem, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Basket( $provider, $context, $this->couponItem, 'abcd' );
		$this->object->setObject( $this->object );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $context );
		$search = $productManager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC' ) ) );
		$products = $productManager->search( $search )->toArray();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$price = $priceManager->create();
		$price->setValue( 321 );

		foreach( $products as $product )
		{
			$orderProduct = $orderProductManager->create();
			$orderProduct->copyFrom( $product );
			$orderProducts[$product->getCode()] = $orderProduct;
		}

		$orderProducts['CNC']->setPrice( $price );

		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->create(), $context->getLocale() );
		$this->orderBase->addProduct( $orderProducts['CNC'] );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
		unset( $this->couponItem );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'basket.total-value-min', $result );
		$this->assertArrayHasKey( 'basket.total-value-max', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'basket.total-value-min' => ['EUR' => '10.5'],
			'basket.total-value-max' => ['EUR' => '100'],
		];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['basket.total-value-min'] );
		$this->assertNull( $result['basket.total-value-max'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( ['basket.total-value-min' => '10.5'] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['basket.total-value-min'] );
		$this->assertNull( $result['basket.total-value-max'] );
	}


	public function testIsAvailable()
	{
		$config = array(
			'basket.total-value-min' => array( 'EUR' =>  320 ),
			'basket.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertTrue( $result );
	}

	// min value higher than order price
	public function testIsAvailableTestMinValue()
	{
		$config = array(
			'basket.total-value-min' => array( 'EUR' =>  700 ),
			'basket.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertFalse( $result );
	}

	// order price higher than max price
	public function testIsAvailableTestMaxValue()
	{
		$config = array(
			'basket.total-value-min' => array( 'EUR' =>  50 ),
			'basket.total-value-max' => array( 'EUR' => 320 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertFalse( $result );
	}

}

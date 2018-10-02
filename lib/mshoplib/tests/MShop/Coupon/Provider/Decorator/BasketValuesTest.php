<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class BasketValuesTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;
	private $couponItem;


	protected function setUp()
	{
		$orderProducts = [];
		$context = \TestHelperMShop::getContext();

		$couponManager = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $context );
		$this->couponItem = $couponManager->createItem();

		$provider = new \Aimeos\MShop\Coupon\Provider\Example( $context, $this->couponItem, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\BasketValues( $provider, $context, $this->couponItem, 'abcd' );
		$this->object->setObject( $this->object );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC' ) ) );
		$products = $productManager->searchItems( $search );

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$price = $priceManager->createItem();
		$price->setValue( 321 );

		foreach( $products as $product )
		{
			$orderProduct = $orderProductManager->createItem();
			$orderProduct->copyFrom( $product );
			$orderProducts[$product->getCode()] = $orderProduct;
		}

		$orderProducts['CNC']->setPrice( $price );

		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
		$this->orderBase->addProduct( $orderProducts['CNC'] );
	}


	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->orderBase );
		unset( $this->couponItem );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'basketvalues.total-value-min', $result );
		$this->assertArrayHasKey( 'basketvalues.total-value-max', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'basketvalues.total-value-min' => ['EUR' => '10.5'],
			'basketvalues.total-value-max' => ['EUR' => '100'],
		];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'null', $result['basketvalues.total-value-min'] );
		$this->assertInternalType( 'null', $result['basketvalues.total-value-max'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( ['basketvalues.total-value-min' => '10.5'] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['basketvalues.total-value-min'] );
		$this->assertInternalType( 'null', $result['basketvalues.total-value-max'] );
	}


	public function testIsAvailable()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  320 ),
			'basketvalues.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertTrue( $result );
	}

	// min value higher than order price
	public function testIsAvailableTestMinValue()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  700 ),
			'basketvalues.total-value-max' => array( 'EUR' => 1000 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertFalse( $result );
	}

	// order price higher than max price
	public function testIsAvailableTestMaxValue()
	{
		$config = array(
			'basketvalues.total-value-min' => array( 'EUR' =>  50 ),
			'basketvalues.total-value-max' => array( 'EUR' => 320 ),
		);

		$this->couponItem->setConfig( $config );
		$result = $this->object->isAvailable( $this->orderBase );

		$this->assertFalse( $result );
	}

}

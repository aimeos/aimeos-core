<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Reduction.
 */
class MShop_Service_Provider_Decorator_ReductionTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$servManager = MShop_Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Reduction' )
			->disableOriginalConstructor()->getMock();

		$this->basket = MShop_Order_Manager_Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new MShop_Service_Provider_Decorator_Reduction( $this->context, $this->servItem, $this->mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'reduction.percent', $result );
		$this->assertArrayHasKey( 'reduction.basket-value-min', $result );
		$this->assertArrayHasKey( 'reduction.basket-value-max', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'reduction.percent' => '1.5',
			'reduction.basket-value-min' => array( 'EUR' => '10.00' ),
			'reduction.basket-value-max' => array( 'EUR' => '100.00' ),
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'null', $result['reduction.percent'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-min'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-max'] );
	}


	public function testCheckConfigBEFailurePercentage()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->object->checkConfigBE( array() );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'null', $result['reduction.percent'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-min'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-max'] );
	}


	public function testCheckConfigBEFailureBasketValueMin()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-min' => '10.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['reduction.basket-value-min'] );
	}


	public function testCheckConfigBEFailureBasketValueMax()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-max' => '100.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['reduction.basket-value-max'] );
	}


	public function testCalcPrice()
	{
		$this->servItem->setConfig( array( 'reduction.percent' => 50 ) );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '5.00', $price->getCosts() );
		$this->assertEquals( '5.00', $price->getRebate() );
	}


	public function testCalcPriceMin()
	{
		$config = array( 'reduction.percent' => 50, 'reduction.basket-value-min' => array( 'EUR' => '20.00' ) );
		$this->servItem->setConfig( $config );
		$this->basket->addProduct( $this->getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '5.00', $price->getCosts() );
		$this->assertEquals( '5.00', $price->getRebate() );
	}


	public function testCalcPriceMinNotReached()
	{
		$config = array( 'reduction.percent' => 50, 'reduction.basket-value-min' => array( 'EUR' => '20.01' ) );
		$this->servItem->setConfig( $config );
		$this->basket->addProduct( $this->getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '10.00', $price->getCosts() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	public function testCalcPriceMax()
	{
		$config = array( 'reduction.percent' => 50, 'reduction.basket-value-max' => array( 'EUR' => '20.00' ) );
		$this->servItem->setConfig( $config );
		$this->basket->addProduct( $this->getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '5.00', $price->getCosts() );
		$this->assertEquals( '5.00', $price->getRebate() );
	}


	public function testCalcPriceMaxExceeded()
	{
		$config = array( 'reduction.percent' => 50, 'reduction.basket-value-max' => array( 'EUR' => '19.99' ) );
		$this->servItem->setConfig( $config );
		$this->basket->addProduct( $this->getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '10.00', $price->getCosts() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	protected function getOrderProduct()
	{
		$priceManager = MShop_Factory::createManager( $this->context, 'price' );
		$productManager = MShop_Factory::createManager( $this->context, 'product' );
		$orderProductManager = MShop_Factory::createManager( $this->context, 'order/base/product' );

		$price = $priceManager->createItem();
		$price->setValue( '20.00' );

		$product = $productManager->createItem();
		$product->setCode( 'test' );

		$orderProduct = $orderProductManager->createItem();
		$orderProduct->copyFrom( $product );
		$orderProduct->setPrice( $price );

		return $orderProduct;
	}
}
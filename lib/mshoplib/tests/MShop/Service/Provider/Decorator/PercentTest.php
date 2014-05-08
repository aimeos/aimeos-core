<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Percent.
 */
class MShop_Service_Provider_Decorator_PercentTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_basket;
	private $_context;
	private $_servItem;
	private $_mockProvider;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$servManager = MShop_Factory::createManager( $this->_context, 'service' );
		$this->_servItem = $servManager->createItem();

		$this->_mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Percent' )
			->disableOriginalConstructor()->getMock();

		$this->_basket = MShop_Order_Manager_Factory::createManager( $this->_context )
			->getSubManager( 'base' )->createItem();

		$this->_object = new MShop_Service_Provider_Decorator_Percent( $this->_context, $this->_servItem, $this->_mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->_object, $this->_basket, $this->_mockProvider, $this->_servItem, $this->_context );
	}


	public function testGetConfigBE()
	{
		$result = $this->_object->getConfigBE();

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'percent.percentage', $result );
		$this->assertArrayHasKey( 'percent.basket-value-min', $result );
		$this->assertArrayHasKey( 'percent.basket-value-max', $result );
	}


	public function testCheckConfigBE()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array(
			'percent.percentage' => '1.5',
			'percent.basket-value-min' => array( 'EUR' => '10.00' ),
			'percent.basket-value-max' => array( 'EUR' => '100.00' ),
		);
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'null', $result['percent.percentage'] );
		$this->assertInternalType( 'null', $result['percent.basket-value-min'] );
		$this->assertInternalType( 'null', $result['percent.basket-value-max'] );
	}


	public function testCheckConfigBEFailurePercentage()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->checkConfigBE( array() );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['percent.percentage'] );
	}


	public function testCheckConfigBEFailureBasketValueMin()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->checkConfigBE( array( 'percent.basket-value-min' => '10.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['percent.basket-value-min'] );
	}


	public function testCheckConfigBEFailureBasketValueMax()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->checkConfigBE( array( 'percent.basket-value-max' => '100.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['percent.basket-value-max'] );
	}


	public function testCalcPrice()
	{
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$this->_servItem->setConfig( array( 'percent.percentage' => 1.5 ) );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
	}


	public function testCalcPriceMin()
	{
		$config = array( 'percent.percentage' => 1.5, 'percent.basket-value-min' => array( 'EUR' => '20.00' ) );
		$this->_servItem->setConfig( $config );
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
	}


	public function testCalcPriceMinNotReached()
	{
		$config = array( 'percent.percentage' => 1.5, 'percent.basket-value-min' => array( 'EUR' => '20.01' ) );
		$this->_servItem->setConfig( $config );
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.00', $price->getCosts() );
	}


	public function testCalcPriceMax()
	{
		$config = array( 'percent.percentage' => 1.5, 'percent.basket-value-max' => array( 'EUR' => '20.00' ) );
		$this->_servItem->setConfig( $config );
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
	}


	public function testCalcPriceMaxExceeded()
	{
		$config = array( 'percent.percentage' => 1.5, 'percent.basket-value-max' => array( 'EUR' => '19.99' ) );
		$this->_servItem->setConfig( $config );
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.00', $price->getCosts() );
	}


	protected function _getOrderProduct()
	{
		$priceManager = MShop_Factory::createManager( $this->_context, 'price' );
		$productManager = MShop_Factory::createManager( $this->_context, 'product' );
		$orderProductManager = MShop_Factory::createManager( $this->_context, 'order/base/product' );

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
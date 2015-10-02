<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Costs.
 */
class MShop_Service_Provider_Decorator_CostsTest extends PHPUnit_Framework_TestCase
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

		$this->mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Costs' )
			->disableOriginalConstructor()->getMock();

		$this->basket = MShop_Order_Manager_Factory::createManager( $this->context )
			->getSubManager( 'base' )->createItem();

		$this->object = new MShop_Service_Provider_Decorator_Costs( $this->context, $this->servItem, $this->mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'costs.percent', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'costs.percent' => '1.5' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'null', $result['costs.percent'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->object->checkConfigBE( array() );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['costs.percent'] );
	}


	public function testCalcPrice()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( array( 'costs.percent' => 1.5 ) );
		$priceItem = MShop_Factory::createManager( $this->context, 'price' )->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
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
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Provider_Decorator_Costs.
 */
class MShop_Service_Provider_Decorator_CostsTest extends MW_Unittest_Testcase
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

		$this->_mockProvider = $this->getMockBuilder( 'MShop_Service_Provider_Decorator_Costs' )
			->disableOriginalConstructor()->getMock();

		$this->_basket = MShop_Order_Manager_Factory::createManager( $this->_context )
			->getSubManager( 'base' )->createItem();

		$this->_object = new MShop_Service_Provider_Decorator_Costs( $this->_context, $this->_servItem, $this->_mockProvider );
	}


	protected function tearDown()
	{
		unset( $this->_object, $this->_basket, $this->_mockProvider, $this->_servItem, $this->_context );
	}


	public function testGetConfigBE()
	{
		$result = $this->_object->getConfigBE();

		$this->assertArrayHasKey( 'costs.percent', $result );
	}


	public function testCheckConfigBE()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$attributes = array( 'costs.percent' => '1.5' );
		$result = $this->_object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'null', $result['costs.percent'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->_mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( array() ) );

		$result = $this->_object->checkConfigBE( array() );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['costs.percent'] );
	}


	public function testCalcPrice()
	{
		$this->_basket->addProduct( $this->_getOrderProduct() );
		$this->_servItem->setConfig( array( 'costs.percent' => 1.5 ) );
		$priceItem = MShop_Factory::createManager( $this->_context, 'price' )->createItem();

		$this->_mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->_object->calcPrice( $this->_basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
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
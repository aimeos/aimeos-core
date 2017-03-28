<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Reduction.
 */
class ReductionTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop\Factory::createManager( $this->context, 'service' );
		$this->servItem = $servManager->createItem();

		$this->mockProvider = $this->getMockBuilder( '\\Aimeos\\MShop\\Service\\Provider\\Decorator\\Reduction' )
			->disableOriginalConstructor()->getMock();

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $this->context );
		$this->basket = $orderManager->getSubManager( 'base' )->createItem();
		$this->basket->__sleep(); // remove plugins

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Reduction( $this->mockProvider, $this->context, $this->servItem );
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
			->will( $this->returnValue( [] ) );

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
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'null', $result['reduction.percent'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-min'] );
		$this->assertInternalType( 'null', $result['reduction.basket-value-max'] );
	}


	public function testCheckConfigBEFailureBasketValueMin()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-min' => '10.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['reduction.basket-value-min'] );
	}


	public function testCheckConfigBEFailureBasketValueMax()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-max' => '100.00' ) );

		$this->assertEquals( 3, count( $result ) );
		$this->assertInternalType( 'string', $result['reduction.basket-value-max'] );
	}


	public function testCalcPrice()
	{
		$this->servItem->setConfig( array( 'reduction.percent' => 50 ) );
		$priceItem = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '10.00', $price->getCosts() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	/**
	 * Returns an order product item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProduct()
	{
		$priceManager = \Aimeos\MShop\Factory::createManager( $this->context, 'price' );
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$orderProductManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );

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
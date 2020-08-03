<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class ReductionTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $basket;
	private $context;
	private $servItem;
	private $mockProvider;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();

		$servManager = \Aimeos\MShop::create( $this->context, 'service' );
		$this->servItem = $servManager->createItem()->setId( -1 );

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Reduction::class )
			->disableOriginalConstructor()->getMock();

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$this->basket = $orderManager->getSubManager( 'base' )->createItem()->off(); // remove plugins

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Reduction( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( 'reduction.percent', $result );
		$this->assertArrayHasKey( 'reduction.product-costs', $result );
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
			'reduction.product-costs' => '1',
			'reduction.basket-value-min' => array( 'EUR' => '10.00' ),
			'reduction.basket-value-max' => array( 'EUR' => '100.00' ),
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertNull( $result['reduction.percent'] );
		$this->assertNull( $result['reduction.product-costs'] );
		$this->assertNull( $result['reduction.basket-value-min'] );
		$this->assertNull( $result['reduction.basket-value-max'] );
	}


	public function testCheckConfigBEFailurePercentage()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 4, count( $result ) );
		$this->assertIsString( $result['reduction.percent'] );
		$this->assertNull( $result['reduction.product-costs'] );
		$this->assertNull( $result['reduction.basket-value-min'] );
		$this->assertNull( $result['reduction.basket-value-max'] );
	}


	public function testCheckConfigBEFailureBasketValueMin()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-min' => '10.00' ) );

		$this->assertEquals( 4, count( $result ) );
		$this->assertIsString( $result['reduction.basket-value-min'] );
	}


	public function testCheckConfigBEFailureBasketValueMax()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( array( 'reduction.basket-value-max' => '100.00' ) );

		$this->assertEquals( 4, count( $result ) );
		$this->assertIsString( $result['reduction.basket-value-max'] );
	}


	public function testCalcPrice()
	{
		$this->servItem->setConfig( array( 'reduction.percent' => 50 ) );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();
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
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();
		$priceItem->setCosts( '10.00' );

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '10.00', $price->getCosts() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	public function testCalcPriceProductCosts()
	{
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem()->setCosts( '10.00' );
		$orderProduct = $this->getOrderProduct();
		$orderProduct->setPrice( $orderProduct->getPrice()->setCosts( '10.00' ) );
		$subProduct = $this->getOrderProduct();
		$subProduct->setPrice( $subProduct->getPrice()->setCosts( '5.00' ) );

		$this->servItem->setConfig( ['reduction.percent' => 60, 'reduction.product-costs' => 1] );
		$this->basket->addProduct( $orderProduct->setProducts( [$subProduct] ) );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '-5.00', $price->getCosts() );
		$this->assertEquals( '15.00', $price->getRebate() );
	}


	/**
	 * Returns an order product item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProduct()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$orderProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );

		$price = $priceManager->createItem();
		$price->setValue( '20.00' );

		$product = $productManager->createItem()->setId( '-1' );
		$product->setCode( 'test' )->setType( 'test' );

		$orderProduct = $orderProductManager->createItem();
		$orderProduct->copyFrom( $product );
		$orderProduct->setPrice( $price );

		return $orderProduct;
	}
}

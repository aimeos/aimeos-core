<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
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
		$this->servItem = $servManager->create()->setId( -1 );

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Reduction::class )
			->disableOriginalConstructor()->getMock();

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$this->basket = $orderManager->getSubManager( 'base' )->create()->off(); // remove plugins

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
		$this->assertArrayHasKey( 'reduction.include-costs', $result );
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
			'reduction.include-costs' => '1',
			'reduction.basket-value-min' => array( 'EUR' => '10.00' ),
			'reduction.basket-value-max' => array( 'EUR' => '100.00' ),
		);
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertNull( $result['reduction.percent'] );
		$this->assertNull( $result['reduction.include-costs'] );
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
		$this->assertNull( $result['reduction.include-costs'] );
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
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( ['reduction.percent' => 10] );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );

		$this->assertEquals( '18.00', $price->getValue() );
		$this->assertEquals( '2.00', $price->getRebate() );
	}


	public function testCalcPriceMin()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( ['reduction.percent' => 10, 'reduction.basket-value-min' => ['EUR' => '20.00']] );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );

		$this->assertEquals( '18.00', $price->getValue() );
		$this->assertEquals( '2.00', $price->getRebate() );
	}


	public function testCalcPriceMinNotReached()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( ['reduction.percent' => 10, 'reduction.basket-value-min' => ['EUR' => '20.01']] );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );

		$this->assertEquals( '20.00', $price->getValue() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	public function testCalcPriceMax()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( ['reduction.percent' => 10, 'reduction.basket-value-max' => ['EUR' => '20.00']] );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );

		$this->assertEquals( '18.00', $price->getValue() );
		$this->assertEquals( '2.00', $price->getRebate() );
	}


	public function testCalcPriceMaxExceeded()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( ['reduction.percent' => 10, 'reduction.basket-value-max' => ['EUR' => '19.99']] );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );

		$this->assertEquals( '20.00', $price->getValue() );
		$this->assertEquals( '0.00', $price->getRebate() );
	}


	public function testCalcPriceIncludeCosts()
	{
		$this->servItem->setConfig( ['reduction.percent' => 10, 'reduction.include-costs' => 1] );

		$orderProduct = $this->getOrderProduct()->setQuantity( 2 );
		$this->basket->addProduct( $orderProduct->setPrice( $orderProduct->getPrice()->setCosts( '10.00' ) ) );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '40.00' )->setCosts( '20.00' );

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '36.00', $price->getValue() );
		$this->assertEquals( '18.00', $price->getCosts() );
		$this->assertEquals( '6.00', $price->getRebate() );
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

		$price = $priceManager->create()->setValue( '20.00' );
		$product = $productManager->create()->setId( '-1' )->setCode( 'test' )->setType( 'test' );
		$orderProduct = $orderProductManager->create()->copyFrom( $product )->setPrice( $price );

		return $orderProduct;
	}
}

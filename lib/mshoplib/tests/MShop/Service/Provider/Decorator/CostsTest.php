<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Test class for \Aimeos\MShop\Service\Provider\Decorator\Costs.
 */
class CostsTest extends \PHPUnit\Framework\TestCase
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

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Costs::class )
			->disableOriginalConstructor()->getMock();

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$this->basket = $orderManager->getSubManager( 'base' )->createItem()->off(); // remove plugins

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Costs( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testGetConfigBE()
	{
		$this->mockProvider->expects( $this->once() )->method( 'getConfigBE' )->will( $this->returnValue( [] ) );

		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'costs.percent', $result );
	}


	public function testCheckConfigBE()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$attributes = array( 'costs.percent' => '1.5' );
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['costs.percent'] );
	}


	public function testCheckConfigBEFailure()
	{
		$this->mockProvider->expects( $this->once() )
			->method( 'checkConfigBE' )
			->will( $this->returnValue( [] ) );

		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['costs.percent'] );
	}


	public function testCalcPrice()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$this->servItem->setConfig( array( 'costs.percent' => 1.5 ) );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->createItem();

		$this->mockProvider->expects( $this->once() )
			->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '0.30', $price->getCosts() );
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

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


class NocostsTest extends \PHPUnit\Framework\TestCase
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

		$this->mockProvider = $this->getMockBuilder( \Aimeos\MShop\Service\Provider\Decorator\Nocosts::class )
			->disableOriginalConstructor()->getMock();

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context );
		$this->basket = $orderManager->getSubManager( 'base' )->create()->off(); // remove plugins

		$this->object = new \Aimeos\MShop\Service\Provider\Decorator\Nocosts( $this->mockProvider, $this->context, $this->servItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->basket, $this->mockProvider, $this->servItem, $this->context );
	}


	public function testCalcPrice()
	{
		$this->basket->addProduct( $this->getOrderProduct() );
		$priceItem = \Aimeos\MShop::create( $this->context, 'price' )->create();

		$this->mockProvider->expects( $this->once() )->method( 'calcPrice' )
			->will( $this->returnValue( $priceItem->setValue( 0 ) ) );

		$price = $this->object->calcPrice( $this->basket );
		$this->assertEquals( '0.00', $price->getValue() );
		$this->assertEquals( '-5.00', $price->getCosts() );
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

		$price = $priceManager->create();
		$price->setValue( '20.00' )->setCosts( '5.00' );

		$product = $productManager->create()->setId( '-1' );
		$product->setCode( 'test' )->setType( 'test' );

		$orderProduct = $orderProductManager->create();
		$orderProduct->copyFrom( $product );
		$orderProduct->setPrice( $price );

		return $orderProduct;
	}
}

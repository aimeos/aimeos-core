<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductPriceTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $order;
	private $plugin;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create()->setConfig( ['warn' => true] );
		$this->order = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off(); // remove event listeners

		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$search = $orderBaseProductManager->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNC' ) );
		$orderProducts = $orderBaseProductManager->search( $search )->toArray();

		if( ( $orderProduct = reset( $orderProducts ) ) === false ) {
			throw new \RuntimeException( 'No order base product item found.' );
		}

		$price = $orderProduct->getPrice();
		$price = $price->setValue( 600.00 )->setCosts( 30.00 )->setRebate( 0.00 )->setTaxrate( 19.00 );

		$orderProduct = $orderProduct->setPrice( $price );
		$this->order->addProduct( $orderProduct );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->plugin, $this->order, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'ignore-modified' => '0',
			'warn' => '1',
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['warn'] );
		$this->assertEquals( null, $result['ignore-modified'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'warn', $list );
		$this->assertArrayHasKey( 'ignore-modified', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}

	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateArticlePriceCorrect()
	{
		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	public function testUpdateSelectionPriceCorrect()
	{
		$productItem = \Aimeos\MShop::create( $this->context, 'product' )->find( 'U:TEST', ['price'] );
		$refPrices = $productItem->getRefItems( 'price', 'default', 'default' );

		if( ( $productPrice = $refPrices->first() ) === null ) {
			throw new \RuntimeException( 'No product price available' );
		}


		$orderProduct = $this->order->getProduct( 0 )->setProductId( $productItem->getId() )
			->setProductCode( 'U:TESTSUB02' )->setPrice( $productPrice );

		$this->order->addProduct( $orderProduct, 0 );
		$this->plugin->setConfig( array( 'update' => true ) );
		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	public function testUpdateArticlePriceUpdated()
	{
		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct->setPrice( $orderProduct->getPrice()->setValue( 13.13 ) );
		$this->order->addProduct( $orderProduct, 0 );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '600.00', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( ['product' => ['0' => 'price.changed']], $mppe->getErrorCodes() );
		}
	}


	public function testUpdateSelectionPriceUpdated()
	{
		$productItem = \Aimeos\MShop::create( $this->context, 'product' )->find( 'U:TEST' );

		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct = $orderProduct->setProductCode( 'U:TESTSUB02' )
			->setProductId( $productItem->getId() )->setPrice( $orderProduct->getPrice() );

		$this->order->addProduct( $orderProduct, 0 );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '18.00', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( ['product' => ['0' => 'price.changed']], $mppe->getErrorCodes() );
		}
	}


	public function testUpdateAttributePriceUpdated()
	{
		$attribute = \Aimeos\MShop::create( $this->context, 'attribute' )
			->find( 'xs', ['price'], 'product', 'size' );

		$ordAttr = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create()
			->copyFrom( $attribute )->setQuantity( 2 );

		$orderProduct = $this->order->getProduct( 0 )->setAttributeItems( [$ordAttr] );
		$this->order->addProduct( $orderProduct, 0 );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '625.90', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( ['product' => ['0' => 'price.changed']], $mppe->getErrorCodes() );
		}
	}


	public function testUpdateNoPriceChange()
	{
		$orderProduct = $this->order->getProduct( 0 );

		$refPrice = $orderProduct->getPrice()->getValue();
		$orderProduct->setPrice( $orderProduct->getPrice()->setValue( 13.13 ) );

		$this->order->addProduct( $orderProduct, 0 );

		try
		{
			$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
			$this->object->update( $this->order, 'check.after', $part );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$product = $this->order->getProduct( 0 );

			$this->assertEquals( $refPrice, $product->getPrice()->getValue() );
			$this->assertEquals( ['product' => ['0' => 'price.changed']], $mppe->getErrorCodes() );
		}
	}


	public function testUpdatePriceImmutable()
	{
		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct = $orderProduct->setPrice( $orderProduct->getPrice()->setValue( 13.13 ) )
			->setFlags( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE );

		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$oldPrice = clone $this->order->getProduct( 0 )->getPrice();

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
		$this->assertEquals( $oldPrice, $orderProduct->getPrice() );
	}


	public function testIgnoreModified()
	{
		$this->plugin->setConfig( array( 'ignore-modified' => true ) );

		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct->setPrice( $orderProduct->getPrice()->setValue( 13.13 ) );

		$part = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$oldPrice = clone $this->order->getProduct( 0 )->getPrice();

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
		$this->assertEquals( $oldPrice, $orderProduct->getPrice() );
	}
}

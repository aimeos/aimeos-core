<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductFreeOptionsTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductFreeOptions( $this->context, $plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testRegister()
	{
		$order = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $order ) );
	}


	public function testUpdate()
	{
		$prodManager = \Aimeos\MShop::create( $this->context, 'product' );
		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$basket = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off();
		$product = \Aimeos\MShop::create( $this->context, 'order/base/product' )->create();
		$attribute = \Aimeos\MShop::create( $this->context, 'order/base/product/attribute' )->create();

		$attribute = $attribute->setQuantity( 2 )->setCode( 'size' )->setType( 'config' )
			->setAttributeId( $attrManager->find( 'xs', [], 'product', 'size' )->getId() );

		$product = $product->setAttributeItem( $attribute )->setProductId( $prodManager->find( 'CNE' )->getId() );

		$this->assertEquals( $product, $this->object->update( $basket, 'addProduct.after', $product ) );
		$this->assertEquals( [$product], $this->object->update( $basket, 'addProduct.after', [$product] ) );

		$this->assertEquals( '30.95', $product->getPrice()->getValue() );
	}


	public function testAddPrices()
	{
		$price = \Aimeos\MShop::create( $this->context, 'price' )->create()->setValue( '10.00' );

		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$attrItem = $attrManager->find( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->find( 'xl', ['price'], 'product', 'size' );

		$quantities = [$attrItem->getId() => 2, $attrItem2->getId() => 1];
		$attrItems = [$attrItem->getId() => $attrItem, $attrItem2->getId() => $attrItem2];

		$price = $this->access( 'addPrices' )->invokeArgs( $this->object, [$price, $attrItems, $quantities, 2] );

		$this->assertEquals( '25.00', $price->getValue() );
	}


	public function testSortByPrice()
	{
		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$attrItem = $attrManager->find( 'xs', ['price'], 'product', 'size' );

		$quantities = [1 => 2];
		$attrItems = [1 => $attrItem];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [1], array_keys( $items ) );
	}


	public function testSortByPriceFirstNoPrice()
	{
		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$attrItem1 = $attrManager->find( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->find( 's', [], 'product', 'size' );

		$quantities = [1 => 1, 2 => 1];
		$attrItems = [1 => $attrItem2, 2 => $attrItem1];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [2, 1], array_keys( $items ) );
	}


	public function testSortByPriceSecondNoPrice()
	{
		$attrManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$attrItem1 = $attrManager->find( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->find( 's', [], 'product', 'size' );

		$quantities = [1 => 1, 2 => 1];
		$attrItems = [1 => $attrItem1, 2 => $attrItem2];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [1, 2], array_keys( $items ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Plugin\Provider\Order\ProductFreeOptions::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductFreeOptionsTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $plugin;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $this->context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductFreeOption' );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductFreeOptions( $this->context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->object, $this->plugin );
	}


	public function testRegister()
	{
		$basket = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();

		$this->object->register( $basket );
	}


	public function testUpdate()
	{
		$prodManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );
		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );

		$basket = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();
		$product = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' )->createItem();
		$attribute = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product/attribute' )->createItem();

		$attribute->setQuantity( 2 );
		$attribute->setCode( 'size' );
		$attribute->setType( 'config' );
		$attribute->setAttributeId( $attrManager->findItem( 'xs', [], 'product', 'size')->getId() );

		$product->setAttributeItem( $attribute );
		$product->setProductId( $prodManager->findItem( 'CNE' )->getId() );

		$this->object->update( $basket, 'addProduct.after', $product );

		$this->assertEquals( '30.95', $product->getPrice()->getValue() );
	}


	public function testAddPrices()
	{
		$price = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();
		$price->setValue( '10.00' );

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem = $attrManager->findItem( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->findItem( 'xl', ['price'], 'product', 'size' );

		$quantities = [$attrItem->getId() => 2, $attrItem2->getId() => 1];
		$attrItems = [$attrItem->getId() => $attrItem, $attrItem2->getId() => $attrItem2];

		$price = $this->access( 'addPrices' )->invokeArgs( $this->object, [$price, $attrItems, $quantities, 2] );

		$this->assertEquals( '25.00', $price->getValue() );
	}


	public function testSortByPrice()
	{
		$price = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem = $attrManager->findItem( 'xs', ['price'], 'product', 'size' );

		$quantities = [1 => 2];
		$attrItems = [1 => $attrItem];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [1], array_keys( $items ) );
	}


	public function testSortByPriceFirstNoPrice()
	{
		$price = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem1 = $attrManager->findItem( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->findItem( 's', [], 'product', 'size' );

		$quantities = [1 => 1, 2 => 1];
		$attrItems = [1 => $attrItem2, 2 => $attrItem1];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [2, 1], array_keys( $items ) );
	}


	public function testSortByPriceSecondNoPrice()
	{
		$price = \Aimeos\MShop\Factory::createManager( $this->context, 'price' )->createItem();

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->context, 'attribute' );
		$attrItem1 = $attrManager->findItem( 'xs', ['price'], 'product', 'size' );
		$attrItem2 = $attrManager->findItem( 's', [], 'product', 'size' );

		$quantities = [1 => 1, 2 => 1];
		$attrItems = [1 => $attrItem1, 2 => $attrItem2];

		$items = $this->access( 'sortByPrice' )->invokeArgs( $this->object, [$attrItems, $quantities] );

		$this->assertEquals( [1, 2], array_keys( $items ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Plugin\Provider\Order\ProductFreeOptions' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}
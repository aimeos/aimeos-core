<?php

namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class ProductPriceTest extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $price;
	private $plugin;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = \TestHelper::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductPrice' );
		$this->plugin->setStatus( 1 );

		$this->order = \Aimeos\MShop\Factory::createManager( $context, 'order/base' )->createItem();

		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );
		$search = $orderBaseProductManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNC' ) );
		$productItems = $orderBaseProductManager->searchItems( $search );

		if( ( $productItem = reset( $productItems ) ) === false ) {
			throw new \Exception( 'No order base product item found.' );
		}

		$productItem->getPrice()->setValue( 600.00 );
		$productItem->getPrice()->setCosts( 30.00 );
		$productItem->getPrice()->setRebate( 0.00 );
		$productItem->getPrice()->setTaxrate( 19.00 );

		$this->order->addProduct( $productItem );

		$this->price = clone $productItem->getPrice();
		$this->price->setValue( 13.13 );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->plugin );
		unset( $this->order );
		unset( $this->price );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateArticlePriceCorrect()
	{
		$this->plugin->setConfig( array( 'update' => true ) );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) );
	}


	public function testUpdateSelectionPriceCorrect()
	{
		$productManager = \Aimeos\MShop\Factory::createManager( \TestHelper::getContext(), 'product' );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );
		$result = $productManager->searchItems( $search, array( 'price' ) );

		if( ( $productItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No product found' );
		}

		$refPrices = $productItem->getRefItems( 'price', 'default', 'default' );

		if( ( $productPrice = reset( $refPrices ) ) === false ) {
			throw new \Exception( 'No product price available' );
		}


		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct->setProductId( $productItem->getId() );
		$orderProduct->setProductCode( 'U:TESTSUB02' );
		$orderProduct->setPrice( $productPrice );

		$this->plugin->setConfig( array( 'update' => true ) );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) );
	}


	public function testUpdateArticlePriceUpdated()
	{
		$this->order->getProduct( 0 )->setPrice( $this->price );

		$this->plugin->setConfig( array( 'update' => true ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '600.00', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array( 'product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateSelectionPriceUpdated()
	{
		$productManager = \Aimeos\MShop\Factory::createManager( \TestHelper::getContext(), 'product' );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );
		$result = $productManager->searchItems( $search, array( 'price' ) );

		if( ( $productItem = reset( $result ) ) === false ) {
			throw new \Exception( 'No product found' );
		}

		$refPrices = $productItem->getRefItems( 'price', 'default', 'default' );

		if( ( $productPrice = reset( $refPrices ) ) === false ) {
			throw new \Exception( 'No product price available' );
		}


		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct->setProductId( $productItem->getId() );
		$orderProduct->setProductCode( 'U:TESTSUB02' );
		$orderProduct->setPrice( $this->price );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '18.00', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array( 'product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateAttributePriceUpdated()
	{
		$context = \TestHelper::getContext();

		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'xs' ),
			$search->compare( '==', 'attribute.type.code', 'size' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attrManager->searchItems( $search, array( 'price' ) );

		if( ( $attribute = reset( $attributes ) ) === false ) {
			throw new \Exception( 'No attribute found' );
		}

		$orderProdAttrManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product/attribute' );
		$ordAttr = $orderProdAttrManager->createItem();
		$ordAttr->copyFrom( $attribute );

		$orderProduct = $this->order->getProduct( 0 );
		$orderProduct->setAttributes( array( $ordAttr ) );
		$orderProduct->setPrice( $this->price );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( $context, $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$this->assertEquals( '612.95', $this->order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array( 'product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateNoPriceChange()
	{
		$products = $this->order->getProducts();
		if( ( $product = reset( $products ) ) === false ) {
			throw new \Exception( 'There is a product missing from your test data.' );
		}

		$refPrice = $product->getPrice()->getValue();
		$product->setPrice( $this->price );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $mppe )
		{
			$products = $this->order->getProducts();

			if( ( $product = reset( $products ) ) === false ) {
				throw new \Exception( 'No product availalbe' );
			};

			$this->assertEquals( $refPrice, $product->getPrice()->getValue() );
			$this->assertEquals( array( 'product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdatePriceImmutable()
	{
		$products = $this->order->getProducts();

		if( ( $product = reset( $products ) ) === false ) {
			throw new \Exception( 'Product missing from your test data.' );
		}

		$product->setPrice( $this->price );
		$product->setFlags( \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE );

		$oldPrice = clone $product->getPrice();

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductPrice( \TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) );
		$this->assertEquals( $oldPrice, $product->getPrice() );
	}
}
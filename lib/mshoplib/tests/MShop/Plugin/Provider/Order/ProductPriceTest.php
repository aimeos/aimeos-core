<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_ProductPriceTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_price;
	private $_plugin;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'ProductPrice' );
		$this->_plugin->setStatus( 1 );

		$this->_order =  MShop_Factory::createManager( $context, 'order/base' )->createItem();

		$orderBaseProductManager = MShop_Factory::createManager( $context, 'order/base/product' );
		$search = $orderBaseProductManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNC' ) );
		$productItems = $orderBaseProductManager->searchItems( $search );

		if ( ( $productItem = reset( $productItems ) ) === false ) {
			throw new Exception( 'No order base product item found.' );
		}

		$productItem->getPrice()->setValue( 600.00 );
		$productItem->getPrice()->setCosts( 30.00 );
		$productItem->getPrice()->setRebate( 0.00 );
		$productItem->getPrice()->setTaxrate( 19.00 );

		$this->_order->addProduct( $productItem );

		$this->_price = clone $productItem->getPrice();
		$this->_price->setValue( 13.13 );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_plugin );
		unset( $this->_order );
		unset( $this->_price );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);
		$object->register( $this->_order );
	}


	public function testUpdateArticlePriceCorrect()
	{
		$this->_plugin->setConfig( array( 'update' => true ) );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );
	}


	public function testUpdateSelectionPriceCorrect()
	{
		$productManager = MShop_Factory::createManager( TestHelper::getContext(), 'product' );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );
		$result = $productManager->searchItems( $search, array( 'price' ) );

		if( ( $productItem = reset( $result ) ) === false ) {
			throw new Exception( 'No product found' );
		}

		$refPrices = $productItem->getRefItems( 'price', 'default', 'default' );

		if( ( $productPrice = reset( $refPrices ) ) === false ) {
			throw new Exception( 'No product price available' );
		}


		$orderProduct = $this->_order->getProduct( 0 );
		$orderProduct->setProductId( $productItem->getId() );
		$orderProduct->setProductCode( 'U:TESTSUB02' );
		$orderProduct->setPrice( $productPrice );

		$this->_plugin->setConfig( array( 'update' => true ) );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );
	}


	public function testUpdateArticlePriceUpdated()
	{
		$this->_order->getProduct( 0 )->setPrice( $this->_price );

		$this->_plugin->setConfig( array( 'update' => true ) );
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		try
		{
			$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$this->assertEquals( '600.00', $this->_order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array('product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateSelectionPriceUpdated()
	{
		$productManager = MShop_Factory::createManager( TestHelper::getContext(), 'product' );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:TEST' ) );
		$result = $productManager->searchItems( $search, array( 'price' ) );

		if( ( $productItem = reset( $result ) ) === false ) {
			throw new Exception( 'No product found' );
		}

		$refPrices = $productItem->getRefItems( 'price', 'default', 'default' );

		if( ( $productPrice = reset( $refPrices ) ) === false ) {
			throw new Exception( 'No product price available' );
		}


		$orderProduct = $this->_order->getProduct( 0 );
		$orderProduct->setProductId( $productItem->getId() );
		$orderProduct->setProductCode( 'U:TESTSUB02' );
		$orderProduct->setPrice( $this->_price );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		try
		{
			$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$this->assertEquals( '18.00', $this->_order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array('product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateAttributePriceUpdated()
	{
		$context = TestHelper::getContext();

		$attrManager = MShop_Attribute_Manager_Factory::createManager( $context );

		$search = $attrManager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'xs' ),
			$search->compare( '==', 'attribute.type.code', 'size' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$attributes = $attrManager->searchItems( $search, array( 'price' ) );

		if( ( $attribute = reset( $attributes ) ) === false ) {
			throw new Exception( 'No attribute found' );
		}

		$orderProdAttrManager = MShop_Factory::createManager( $context, 'order/base/product/attribute' );
		$ordAttr = $orderProdAttrManager->createItem();
		$ordAttr->copyFrom( $attribute );

		$orderProduct = $this->_order->getProduct( 0 );
		$orderProduct->setAttributes( array( $ordAttr ) );
		$orderProduct->setPrice( $this->_price );

		$object = new MShop_Plugin_Provider_Order_ProductPrice( $context, $this->_plugin );

		try
		{
			$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$this->assertEquals( '612.95', $this->_order->getProduct( 0 )->getPrice()->getValue() );
			$this->assertEquals( array('product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdateNoPriceChange()
	{
		$products = $this->_order->getProducts();
		if( ( $product = reset( $products ) ) === false ) {
			throw new Exception('There is a product missing from your test data.');
		}

		$refPrice = $product->getPrice()->getValue();
		$product->setPrice( $this->_price );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		try
		{
			$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

			$this->fail( 'Price changes not recognized' );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$products = $this->_order->getProducts();

			if( ( $product = reset( $products ) ) === false ) {
				throw new Exception( 'No product availalbe' );
			};

			$this->assertEquals( $refPrice, $product->getPrice()->getValue() );
			$this->assertEquals( array('product' => array( '0' => 'price.changed' ) ), $mppe->getErrorCodes() );
		}
	}


	public function testUpdatePriceImmutable()
	{
		$products = $this->_order->getProducts();

		if ( ( $product = reset( $products ) ) === false ) {
			throw new Exception('Product missing from your test data.');
		}

		$product->setPrice( $this->_price );
		$product->setFlags( Mshop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE );

		$oldPrice = clone $product->getPrice();

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		$this->assertTrue( $object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );
		$this->assertEquals( $oldPrice, $product->getPrice() );
	}
}
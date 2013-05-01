<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @version $Id$
 */

class MShop_Plugin_Provider_Order_ProductPriceTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_product;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_ProductPriceTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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

		$this->_orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->_orderManager->getSubManager('base');

		$this->_order =  $orderBaseManager->createItem();

		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$search = $orderBaseProductManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNC' ) );
		$productItems = $orderBaseProductManager->searchItems( $search, array( 'price' ) );

		if ( ( $productItem = reset( $productItems ) ) === false ) {
			throw new Exception( 'No order base product item found.' );
		}

		$productItem->getPrice()->setValue( 600.00 );
		$productItem->getPrice()->setShipping( 30.00 );
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
		unset( $this->_orderManager );
		unset( $this->_plugin );
		unset( $this->_order );
		unset( $this->_price );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);
		$object->register( $this->_order );
	}

	public function testUpdateNone()
	{
		$products = $this->_order->getProducts();
		if ( ( $product = reset( $products ) ) === false ) {
			throw new Exception('There are products missing from your test data');
		}

		$oldPrice = clone $product->getPrice();

		$this->_plugin->setConfig( array() );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );

		$this->_plugin->setConfig( array( 'update' => false ) );

		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );

		$this->_plugin->setConfig( array( 'update' => true ) );

		// MShop_Order_Item_Base_Abstract::PARTS_PRODUCT not set, so check shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);
		$this->assertTrue( $object->update( $this->_order, 'isComplete.after' ) );

		$this->assertEquals( $oldPrice, $product->getPrice() );
	}

	public function testUpdatePriceCorrect()
	{
		$this->_plugin->setConfig( array( 'update' => true ) );
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );
	}

	public function testUpdatePriceUpdated()
	{
		$refPrices = array();
		foreach ( $this->_order->getProducts() as $id => $product )
		{
			$refPrices[$id] = $product->getPrice()->getValue();
			$product->setPrice( $this->_price );
		}

		$this->_plugin->setConfig( array( 'update' => true ) );
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		try {
			$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$refErrorCodes = array('product' => array( '0' => 'product.price' ) );
			$this->assertEquals( $refErrorCodes, $mppe->getErrorCodes() );

			$currentPrices = array();
			foreach ( $this->_order->getProducts() as $id => $product ) {
				$currentPrices[$id] = $product->getPrice()->getValue();
			}

			$this->assertEquals( $refPrices, $currentPrices );

			return;
		}
		$this->fail( 'Price changes not recognized' );
	}

	public function testUpdateNoPriceChange()
	{
		$products = $this->_order->getProducts();
		if( ( $product = reset( $products ) ) === false ) {
			throw new Exception('There is a product missing from your test data.');
		}

		$refPrice = $product->getPrice()->getValue();

		$product->setPrice( $this->_price );

		$this->_plugin->setConfig( array( 'update' => true ) );
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		try {
			$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch ( MShop_Plugin_Provider_Exception $mppe )
		{
			$refErrorCodes = array('product' => array( '0' => 'product.price' ) );
			$this->assertEquals( $refErrorCodes, $mppe->getErrorCodes() );

			$products = $this->_order->getProducts();
			$product = reset( $products );
			$currentPrice = $product->getPrice()->getValue();

			$this->assertEquals( $refPrice, $currentPrice );

			return;
		}
		$this->fail( 'Price changes not recognized' );
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

		$this->_plugin->setConfig( array( 'update' => true ) );
		$object = new MShop_Plugin_Provider_Order_ProductPrice(TestHelper::getContext(), $this->_plugin);

		$this->assertTrue( $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) );

		$this->assertEquals( $oldPrice, $product->getPrice() );
	}
}
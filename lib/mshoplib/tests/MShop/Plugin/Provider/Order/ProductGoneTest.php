<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_ProductGoneTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_product;
	private $_orderManager;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_ProductGoneTest');
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
		$this->_plugin->setProvider( 'ProductGone' );
		$this->_plugin->setStatus( 1 );

		$this->_orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->_orderManager->getSubManager('base');

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', 672.00 ) );
		$search->setSlice(0, 1);
		$items = $orderBaseManager->searchItems( $search );
		if( ( $baseItem = reset($items) ) === false ) {
			throw new Exception( 'No order base item found.' );
		}

		$this->_order = $baseItem;

		// create a product to mess with in the tests
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$search->setSlice(0, 1);
		$items = $productManager->searchItems( $search );
		if ( ( $newProduct = reset( $items ) ) === false ) {
			throw new Exception( 'Product code "CNE" not found.' );
		}

		$newProduct->setId( null );
		$newProduct->setLabel( 'Bad Product' );
		$newProduct->setCode('WTF');
		$productManager->saveItem( $newProduct );

		$this->_product = $newProduct;
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'WTF' ) );
		$items = $productManager->searchItems( $search );

		foreach ( $items as $badItem ) {
			$productManager->deleteItem( $badItem->getId() );
		}

		unset( $this->_orderManager );
		unset( $this->_plugin );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		$object->register( $this->_order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_PRODUCT not set, so check shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		$this->AssertTrue( $object->update( $this->_order, 'isComplete.after' ) );
	}

	public function testUpdateOk()
	{
		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		$result = $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

		$this->assertTrue( $result );
	}

	public function testUpdateProductDeleted()
	{
		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');

		$badItem = $orderBaseProductManager->createItem();
		$badItem->setProductId( -13 );
		$badItem->setProductCode( 'NONE' );

		$this->_order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
	}

	public function testUpdateProductEnded()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->_product );

		$this->_product->setDateEnd( '1999-12-31 23:59:59' );

		$productManager->saveItem( $this->_product );

		$this->_order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
	}

	public function testUpdateProductNotStarted()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->_product );

		$this->_product->setDateStart( '2022-12-31 23:59:59' );

		$productManager->saveItem( $this->_product );

		$this->_order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
	}

	public function testUpdateProductDeactivated()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->_orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->_product );

		$this->_product->setStatus(0);

		$productManager->saveItem( $this->_product );

		$this->_order->addProduct( $badItem );

		$products = $this->_order->getProducts();
		if ( count( $products ) < 1 ) {
			throw new Exception( 'Product for testing not in basket.' );
		}
		list($badItemPosition, $product) = each( $products );

		$object = new MShop_Plugin_Provider_Order_ProductGone(TestHelper::getContext(), $this->_plugin);
		try {
			$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch( MShop_Plugin_Provider_Exception $e ) {
			$ref = array('product' => array( $badItemPosition => 'gone.status' ) );
			$this->assertEquals( $ref, $e->getErrorCodes() );
			return;
		}

		$this->fail( 'MShop_Plugin_Provider_Exception not thrown.' );
	}
}

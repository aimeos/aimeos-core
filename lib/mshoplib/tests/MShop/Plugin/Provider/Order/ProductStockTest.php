<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @version $Id$
 */

class MShop_Plugin_Provider_Order_ProductStockTest extends PHPUnit_Framework_TestCase
{
	protected $_order;
	protected $_plugin;
	protected $_product;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_ProductStockTest');
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
		$this->_plugin->setProvider( 'ProductCode' );
		$this->_plugin->setStatus( 1 );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager('base');

		$this->_order = $orderBaseManager->createItem();

		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$search = $orderBaseProductManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNE' ) );
		$productItems = $orderBaseProductManager->searchItems( $search );

		if ( ( $productItem = reset( $productItems ) ) == false ) {
			throw new Exception( 'No order base product item found.' );
		}

		$this->_product = $productItem;
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_order );
		unset( $this->_plugin );
		unset( $this->_product );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductStock(TestHelper::getContext(), $this->_plugin);
		$object->register( $this->_order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_PRODUCT not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductStock(TestHelper::getContext(), $this->_plugin);
		$this->AssertTrue( $object->update( $this->_order, 'isComplete.after' ) );
	}

	public function testUpdateOk()
	{
		$object = new MShop_Plugin_Provider_Order_ProductStock(TestHelper::getContext(), $this->_plugin);
		$result = $object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );

		$this->assertTrue( $result );
	}

	public function testUpdateOutOfStock()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$stockManager = $productManager->getSubManager('stock');

		$search = $stockManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.productid', $this->_product->getProductId() ) );
		$stockResult = $stockManager->searchItems( $search );

		if( ( $stockItem = reset( $stockResult ) ) === false ) {
			throw new Exception( 'Stock item not found.' );
		}

		$oldStocklevel = $stockItem->getStocklevel();
		$stockItem->setStocklevel( 5 );
		$stockManager->saveItem( $stockItem );

		$this->_product->setQuantity( 9 );
		$this->_order->addProduct( $this->_product );

		$object = new MShop_Plugin_Provider_Order_ProductStock(TestHelper::getContext(), $this->_plugin);

		try {
			$object->update( $this->_order, 'isComplete.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
		}
		catch( MShop_Plugin_Provider_Exception $mppe )
		{
			$stockItem->setStocklevel( $oldStocklevel );
			$stockManager->saveItem( $stockItem );

			$ref = array( 'product' => array( '0' => 'product.stock' ) );
			$this->assertEquals( $ref, $mppe->getErrorCodes() );

			return;
		}

		$stockItem->setStocklevel( $oldStocklevel );
		$stockManager->saveItem( $stockItem );

		$this->fail( 'Stock problem not recognized.' );
	}
}
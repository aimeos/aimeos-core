<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_ProductStockTest extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;
	private $_context;


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
		$this->_context = TestHelper::getContext();

		$pluginManager = MShop_Factory::createManager( $this->_context, 'plugin' );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'ProductCode' );
		$this->_plugin->setStatus( 1 );

		$orderBaseManager = MShop_Factory::createManager( $this->_context, 'order/base' );
		$this->_order = $orderBaseManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_plugin, $this->_order, $this->_context );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_PRODUCT not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );
		$this->assertTrue( $object->update( $this->_order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$constant = MShop_Order_Item_Base_Abstract::PARTS_PRODUCT;
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );
		$this->assertTrue( $object->update( $this->_order, 'check.after', $constant ) );
	}


	public function testUpdateOutOfStock()
	{
		$this->_order->addProduct( $this->_getOrderProduct( 'EFGH' ) );
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );

		try
		{
			$object->update( $this->_order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
			throw new Exception( 'Expected exception not thrown' );
		}
		catch( MShop_Plugin_Provider_Exception $e )
		{
			$ref = array( 'product' => array( '0' => 'stock.notenough' ) );
			$this->assertEquals( $ref, $e->getErrorCodes() );
		}
	}


	public function testUpdateNoStockItem()
	{
		$const = MShop_Order_Item_Base_Abstract::PARTS_PRODUCT;
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );

		$this->_order->addProduct( $this->_getOrderProduct( 'QRST' ) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', $const ) );
	}


	public function testUpdateStockUnlimited()
	{
		$const = MShop_Order_Item_Base_Abstract::PARTS_PRODUCT;
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->_context, $this->_plugin );

		$this->_order->addProduct( $this->_getOrderProduct( 'MNOP' ) );

		$this->assertTrue( $object->update( $this->_order, 'check.after', $const ) );
	}


	protected function _getOrderProduct( $code )
	{
		$productManager = MShop_Factory::createManager( $this->_context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$productItems = $productManager->searchItems( $search );

		if ( ( $productItem = reset( $productItems ) ) == false ) {
			throw new Exception( 'No product item found' );
		}

		$orderProductManager = MShop_Factory::createManager( $this->_context, 'order/base/product' );
		$orderProductItem = $orderProductManager->createItem();
		$orderProductItem->copyFrom( $productItem );

		return $orderProductItem;
	}
}
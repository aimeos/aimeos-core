<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 */

/**
 * Test class for MShop_Plugin_Provider_Order_Complete.
 */
class MShop_Plugin_Provider_Order_BasketLimitsTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var    MShop_Plugin_Provider_Order_Complete
	 * @access protected
	 */
	private $_object;
	private $_products;
	private $_order;
	private $_expensiveAddProduct;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_BasketLimitsTest');
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

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');

		$this->_order = $orderBaseManager->createItem();

		$orderBaseProductManager = $orderBaseManager->getSubManager('product');
		$search = $orderBaseProductManager->createSearch();

		$search->setConditions( $search->combine('&&', array(
			$search->compare( '==', 'order.base.product.prodcode', array('CNE', 'CNC') ),
			$search->compare( '==', 'order.base.product.price', array('600.00', '36.00') )
		)));
		$items = $orderBaseProductManager->searchItems( $search );

		if ( count( $items ) < 2 ) {
			throw new Exception( 'Please fix the test data in your database.' );
		}

		foreach ( $items as $item ) {
			$this->_products[ $item->getProductCode() ] = $item;
		}

		$this->_products['CNE']->setQuantity( 2 );
		$this->_products['CNC']->setQuantity( 1 );

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );
		$plugin = $pluginManager->createItem();
		$plugin->setTypeId( 2 );
		$plugin->setProvider( 'BasketLimits' );
		$plugin->setConfig( array('minorder'=>'75.00', 'minproducts' => '2' ) );
		$plugin->setStatus( '1' );

		$this->_object = new MShop_Plugin_Provider_Order_BasketLimits(TestHelper::getContext(), $plugin);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
		unset($this->_order);
	}

	public function testRegister()
	{
		$this->_object->register( $this->_order );
	}

	public function testUpdateBothFail()
	{
		try {
			$this->_object->update($this->_order, 'isComplete', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT);
		}
		catch ( MShop_Plugin_Provider_Exception $e )
		{
			$errorCodes = array( 'basket' => array( 'limit.min-value', 'limit.min-products' ) );
			$this->assertEquals( $errorCodes, $e->getErrorCodes() );
			return;
		}
		$this->fail( 'Min-products and min-value should have failed.' );
	}

	public function testUpdateMinProductsFails()
	{
		$this->_order->addProduct( $this->_products['CNC'] );

		try {
			$this->_object->update($this->_order, 'isComplete', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT);
		}
		catch ( MShop_Plugin_Provider_Exception $e )
		{
			$errorCodes = array( 'basket' => array( 'limit.min-products' ) );
			$this->assertEquals( $errorCodes, $e->getErrorCodes() );
			return;
		}
		$this->fail( 'Min-products should have failed.' );
	}

	public function testUpdateMinValueFails()
	{
		$this->_order->addProduct( $this->_products['CNE'] );

		try {
			$this->_object->update($this->_order, 'isComplete', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT);
		}
		catch ( MShop_Plugin_Provider_Exception $e )
		{
			$errorCodes = array( 'basket' => array( 'limit.min-value' ) );
			$this->assertEquals( $errorCodes, $e->getErrorCodes() );
			return;
		}
		$this->fail( 'Min-value should have failed. ' );
	}

	public function testUpdateOk()
	{
		$this->_products['CNE']->setQuantity( 4 );
		$this->_order->addProduct( $this->_products['CNE'] );

		$this->assertTrue($this->_object->update($this->_order, 'isComplete', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT));
	}
}

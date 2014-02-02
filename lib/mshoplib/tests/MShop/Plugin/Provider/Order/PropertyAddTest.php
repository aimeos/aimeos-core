<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Plugin_Provider_Order_PropertyAdd.
 */
class MShop_Plugin_Provider_Order_PropertyAddTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_plugin;
	private $_order;
	private $_products;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_PropertyAddTest');
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
		$pluginManager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'PropertyAdd' );
		$this->_plugin->setStatus( '1' );

		$this->_plugin->setConfig( array( 'product.stock.productid' => array(
			'product.stock.warehouseid',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager('base');
		$orderBaseProductManager = $orderBaseManager->getSubManager('product');

		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC') ) );

		$products = $manager->searchItems( $search );

		if ( count( $products ) !== 2 ) {
			throw new Exception('Wrong number of products');
		}

		$this->_products = array();

		foreach( $products as $product )
		{
			$item = $orderBaseProductManager->createItem();
			$item->copyFrom( $product );

			$this->_products[ $product->getCode() ] = $item;
		}

		$this->_order = $orderBaseManager->createItem();

		$this->_object = new MShop_Plugin_Provider_Order_PropertyAdd( TestHelper::getContext(), $this->_plugin );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_order, $this->_plugin, $this->_products );
		MShop_Factory::clear();
	}


	public function testRegister()
	{
		$this->_object->register( $this->_order );
	}


	public function testUpdateOk()
	{
		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );
		$this->assertEquals( 4, count( $this->_products['CNC']->getAttributes() ) );

		$this->_products['CNE']->setAttributes( array() );
		$this->_plugin->setConfig( array(
			'product.list.parentid' => array(
				'product.list.domain',
			),
			'product.stock.productid' => array(
				'product.stock.stocklevel'
			)
		) );

		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNE'] );

		$this->assertEquals( 2, count( $this->_products['CNE']->getAttributes() ) );
	}


	public function testUpdateAttributeExists()
	{
		$attributeManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubmanager( 'base' )->getSubmanager('product')->getSubmanager('attribute');

		$attribute = $attributeManager->createItem();

		$attribute->setCode( 'product.stock.stocklevel' );
		$attribute->setName( 'product.stock.stocklevel' );
		$attribute->setValue( '1200' );
		$attribute->setType( 'property' );

		$this->_products['CNC']->setAttributes( array( $attribute ) );
		$this->assertEquals( 1, count( $this->_products['CNC']->getAttributes() ) );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );
		$this->assertEquals( 4, count( $this->_products['CNC']->getAttributes() ) );
	}


	public function testUpdateConfigError()
	{
		// Non-existent property:

		$this->_plugin->setConfig( array( 'product.stock.productid' => array(
			'product.stock.quatsch',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );
		$this->assertEquals( 3, count( $this->_products['CNC']->getAttributes() ) );


		// Incorrect key:

		$this->_plugin->setConfig( array( 'stock.productid' => array(
			'stock.warehouseid',
		) ) );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] );
	}
}

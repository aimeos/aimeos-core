<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Plugin_Provider_Order_PropertyMatch.
 */
class MShop_Plugin_Provider_Order_PropertyMatchTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_plugin;
	private $_order;
	private $_products;


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
		$this->_plugin->setTypeId( 2 );
		$this->_plugin->setProvider( 'PropertyMatch' );
		$this->_plugin->setConfig( array( 'product.suppliercode' => 'unitSupplier' ) );
		$this->_plugin->setStatus( '1' );


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

		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

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
	}


	public function testRegister()
	{
		$this->_object->register( $this->_order );
	}


	public function testUpdateOk()
	{
		// single condition
		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );

		$this->_plugin->setConfig( array( 'product.stock.warehouse.code' => 'default' ) );
		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );


		// two conditions
		$this->_plugin->setConfig( array(
			'product.stock.warehouse.code' => 'default',
			'product.suppliercode' => 'unitSupplier',
		) );
		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );
	}


	public function testUpdateFail()
	{
		$this->_plugin->setConfig( array( 'product.suppliercode' => 'wrongSupplier' ) );
		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] );
	}


	public function testUpdateFailMultipleConditions()
	{
		$this->_plugin->setConfig( array(
			'product.stock.warehouse.code' => 'unit_warehouse2',
			'product.suppliercode' => 'wrongSupplier',
		) );
		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] );
	}

	public function testUpdateFailList()
	{
		$this->_plugin->setConfig( array( 'product.list.domain' => 'foobar' ) );
		$this->_object = new MShop_Plugin_Provider_Order_PropertyMatch( TestHelper::getContext(), $this->_plugin );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] );
	}
}

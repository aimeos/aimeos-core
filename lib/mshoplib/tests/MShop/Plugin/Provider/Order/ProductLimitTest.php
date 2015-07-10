<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Plugin_Provider_Order_ProductLimit.
 */
class MShop_Plugin_Provider_Order_ProductLimitTest extends MW_Unittest_Testcase
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
		$this->_plugin->setProvider( 'ProductLimit' );
		$this->_plugin->setConfig( array( 'single-number-max' => 10 ) );
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

		$this->_object = new MShop_Plugin_Provider_Order_ProductLimit( TestHelper::getContext(), $this->_plugin );

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


	public function testUpdateSingleNumberMax()
	{
		$this->_plugin->setConfig( array( 'single-number-max' => 10 ) );


		$this->_products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );


		$this->_products['CNE']->setQuantity( 11 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNE'] );
	}


	public function testUpdateSingleValueMax()
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );

		$this->_plugin->setConfig( array( 'single-value-max' => array( 'EUR' => '10.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->_products['CNC']->setPrice( $price );
		$this->_products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );


		$price = $priceManager->createItem();
		$price->setValue( '3.50' );

		$this->_products['CNE']->setPrice( $price );
		$this->_products['CNE']->setQuantity( 3 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNE'] );
	}


	public function testUpdateTotalNumberMax()
	{
		$this->_plugin->setConfig( array( 'total-number-max' => 10 ) );


		$this->_products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );


		$this->_order->addProduct( $this->_products['CNC'] );
		$this->_products['CNE']->setQuantity( 1 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNE'] );
	}


	public function testUpdateTotalValueMax()
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );

		$this->_plugin->setConfig( array( 'total-value-max' => array( 'EUR' => '110.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '100.00' );

		$this->_products['CNC']->setPrice( $price );
		$this->_products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNC'] ) );


		$this->_order->addProduct( $this->_products['CNC'] );

		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->_products['CNE']->setPrice( $price );
		$this->_products['CNE']->setQuantity( 2 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->_object->update( $this->_order, 'addProduct.before', $this->_products['CNE'] );
	}
}

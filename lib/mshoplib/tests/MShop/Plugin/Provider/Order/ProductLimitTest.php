<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Plugin_Provider_Order_ProductLimit.
 */
class MShop_Plugin_Provider_Order_ProductLimitTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $products;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$pluginManager = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setProvider( 'ProductLimit' );
		$this->plugin->setConfig( array( 'single-number-max' => 10 ) );
		$this->plugin->setStatus( '1' );


		$orderManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );

		$products = $manager->searchItems( $search );

		if( count( $products ) !== 2 ) {
			throw new Exception( 'Wrong number of products' );
		}

		$this->products = array();

		foreach( $products as $product )
		{
			$item = $orderBaseProductManager->createItem();
			$item->copyFrom( $product );

			$this->products[$product->getCode()] = $item;
		}

		$this->order = $orderBaseManager->createItem();

		$this->object = new MShop_Plugin_Provider_Order_ProductLimit( TestHelper::getContext(), $this->plugin );

	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->products );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateSingleNumberMax()
	{
		$this->plugin->setConfig( array( 'single-number-max' => 10 ) );


		$this->products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->products['CNE']->setQuantity( 11 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateSingleValueMax()
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );

		$this->plugin->setConfig( array( 'single-value-max' => array( 'EUR' => '10.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->products['CNC']->setPrice( $price );
		$this->products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$price = $priceManager->createItem();
		$price->setValue( '3.50' );

		$this->products['CNE']->setPrice( $price );
		$this->products['CNE']->setQuantity( 3 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateTotalNumberMax()
	{
		$this->plugin->setConfig( array( 'total-number-max' => 10 ) );


		$this->products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->order->addProduct( $this->products['CNC'] );
		$this->products['CNE']->setQuantity( 1 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateTotalValueMax()
	{
		$priceManager = MShop_Price_Manager_Factory::createManager( TestHelper::getContext() );

		$this->plugin->setConfig( array( 'total-value-max' => array( 'EUR' => '110.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '100.00' );

		$this->products['CNC']->setPrice( $price );
		$this->products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->order->addProduct( $this->products['CNC'] );

		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->products['CNE']->setPrice( $price );
		$this->products['CNE']->setQuantity( 2 );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}
}

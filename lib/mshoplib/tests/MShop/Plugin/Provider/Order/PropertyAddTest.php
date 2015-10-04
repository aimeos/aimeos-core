<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Plugin_Provider_Order_PropertyAdd.
 */
class MShop_Plugin_Provider_Order_PropertyAddTest extends PHPUnit_Framework_TestCase
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
		$this->plugin->setProvider( 'PropertyAdd' );
		$this->plugin->setStatus( '1' );

		$this->plugin->setConfig( array( 'product.stock.productid' => array(
			'product.stock.warehouseid',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

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

		$this->object = new MShop_Plugin_Provider_Order_PropertyAdd( TestHelper::getContext(), $this->plugin );
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


	public function testUpdateOk()
	{
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 4, count( $this->products['CNC']->getAttributes() ) );

		$this->products['CNE']->setAttributes( array() );
		$this->plugin->setConfig( array(
			'product.lists.parentid' => array(
				'product.lists.domain',
			),
			'product.stock.productid' => array(
				'product.stock.stocklevel'
			)
		) );

		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );

		$this->assertEquals( 2, count( $this->products['CNE']->getAttributes() ) );
	}


	public function testUpdateAttributeExists()
	{
		$attributeManager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() )->getSubmanager( 'base' )->getSubmanager( 'product' )->getSubmanager( 'attribute' );

		$attribute = $attributeManager->createItem();

		$attribute->setCode( 'product.stock.stocklevel' );
		$attribute->setName( 'product.stock.stocklevel' );
		$attribute->setValue( '1200' );
		$attribute->setType( 'property' );

		$this->products['CNC']->setAttributes( array( $attribute ) );
		$this->assertEquals( 1, count( $this->products['CNC']->getAttributes() ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 4, count( $this->products['CNC']->getAttributes() ) );
	}


	public function testUpdateConfigError()
	{
		// Non-existent property:

		$this->plugin->setConfig( array( 'product.stock.productid' => array(
			'product.stock.quatsch',
			'product.stock.editor',
			'product.stock.stocklevel',
			'product.stock.dateback'
		) ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
		$this->assertEquals( 3, count( $this->products['CNC']->getAttributes() ) );


		// Incorrect key:

		$this->plugin->setConfig( array( 'stock.productid' => array(
			'stock.warehouseid',
		) ) );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}
}

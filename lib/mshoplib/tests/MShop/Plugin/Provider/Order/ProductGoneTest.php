<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_ProductGoneTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $product;
	private $orderManager;


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
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductGone' );
		$this->plugin->setStatus( 1 );

		$this->orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', 672.00 ) );
		$search->setSlice( 0, 1 );
		$items = $orderBaseManager->searchItems( $search );
		if( ( $baseItem = reset( $items ) ) === false ) {
			throw new Exception( 'No order base item found.' );
		}

		$this->order = $baseItem;

		// create a product to mess with in the tests
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$search->setSlice( 0, 1 );
		$items = $productManager->searchItems( $search );
		if( ( $newProduct = reset( $items ) ) === false ) {
			throw new Exception( 'Product code "CNE" not found.' );
		}

		$newProduct->setId( null );
		$newProduct->setLabel( 'Bad Product' );
		$newProduct->setCode( 'WTF' );
		$productManager->saveItem( $newProduct );

		$this->product = $newProduct;
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

		foreach( $items as $badItem ) {
			$productManager->deleteItem( $badItem->getId() );
		}

		unset( $this->orderManager );
		unset( $this->plugin );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Base::PARTS_PRODUCT not set, so check shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );
		$this->AssertTrue( $object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );
		$result = $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_PRODUCT );

		$this->assertTrue( $result );
	}


	public function testUpdateProductDeleted()
	{
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$badItem = $orderBaseProductManager->createItem();
		$badItem->setProductId( -13 );
		$badItem->setProductCode( 'NONE' );

		$this->order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_PRODUCT );
	}


	public function testUpdateProductEnded()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setDateEnd( '1999-12-31 23:59:59' );

		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );
		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_PRODUCT );
	}


	public function testUpdateProductNotStarted()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setDateStart( '2022-12-31 23:59:59' );

		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );

		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );
		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_PRODUCT );
	}


	public function testUpdateProductDeactivated()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setStatus( 0 );
		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );
		$products = $this->order->getProducts();

		if( count( $products ) < 1 ) {
			throw new Exception( 'Product for testing not in basket.' );
		}

		$badItemPosition = key( $products );

		$object = new MShop_Plugin_Provider_Order_ProductGone( TestHelper::getContext(), $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_PRODUCT );
			$this->fail( 'MShop_Plugin_Provider_Exception not thrown.' );
		}
		catch( MShop_Plugin_Provider_Exception $e )
		{
			$ref = array( 'product' => array( $badItemPosition => 'gone.status' ) );
			$this->assertEquals( $ref, $e->getErrorCodes() );
		}
	}
}

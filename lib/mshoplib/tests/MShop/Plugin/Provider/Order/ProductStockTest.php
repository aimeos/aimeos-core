<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_ProductStockTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();

		$pluginManager = MShop_Factory::createManager( $this->context, 'plugin' );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductCode' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = MShop_Factory::createManager( $this->context, 'order/base' );
		$this->order = $orderBaseManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->plugin, $this->order, $this->context );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Abstract::PARTS_PRODUCT not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$constant = MShop_Order_Item_Base_Abstract::PARTS_PRODUCT;
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after', $constant ) );
	}


	public function testUpdateOutOfStock()
	{
		$this->order->addProduct( $this->getOrderProduct( 'EFGH' ) );
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Abstract::PARTS_PRODUCT );
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
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );

		$this->order->addProduct( $this->getOrderProduct( 'QRST' ) );

		$this->assertTrue( $object->update( $this->order, 'check.after', $const ) );
	}


	public function testUpdateStockUnlimited()
	{
		$const = MShop_Order_Item_Base_Abstract::PARTS_PRODUCT;
		$object = new MShop_Plugin_Provider_Order_ProductStock( $this->context, $this->plugin );

		$this->order->addProduct( $this->getOrderProduct( 'MNOP' ) );

		$this->assertTrue( $object->update( $this->order, 'check.after', $const ) );
	}


	/**
	 * @param string $code
	 */
	protected function getOrderProduct( $code )
	{
		$productManager = MShop_Factory::createManager( $this->context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$productItems = $productManager->searchItems( $search );

		if( ( $productItem = reset( $productItems ) ) == false ) {
			throw new Exception( 'No product item found' );
		}

		$orderProductManager = MShop_Factory::createManager( $this->context, 'order/base/product' );
		$orderProductItem = $orderProductManager->createItem();
		$orderProductItem->copyFrom( $productItem );

		return $orderProductItem;
	}
}
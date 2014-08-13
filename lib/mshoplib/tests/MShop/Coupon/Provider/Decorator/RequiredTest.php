<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Provider_Decorator_Required.
 */
class MShop_Coupon_Provider_Decorator_RequiredTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_orderBase;
	private $_couponItem;


	/**
	 * Sets up the fixture, especially creates products.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$orderProducts = array();
		$context = TestHelper::getContext();
		$this->_couponItem = MShop_Coupon_Manager_Factory::createManager( $context )->createItem();

		$provider = new MShop_Coupon_Provider_Example( $context, $this->_couponItem, 'abcd' );
		$this->_object = new MShop_Coupon_Provider_Decorator_Required( $context, $this->_couponItem, 'abcd', $provider );
		$this->_object->setObject( $this->_object );

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager('base');
		$orderProductManager = $orderBaseManager->getSubManager( 'product' );

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC' ) ) );
		$products = $productManager->searchItems( $search );

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$price = $priceManager->createItem();
		$price->setValue( 321 );

		foreach( $products as $product )
		{
			$orderProduct = $orderProductManager->createItem();
			$orderProduct->copyFrom( $product );
			$orderProducts[ $product->getCode() ] = $orderProduct;
		}

		$orderProducts['CNC']->setPrice( $price );

		$this->_orderBase = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $context->getLocale() );
		$this->_orderBase->addProduct( $orderProducts['CNC'] );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		unset( $this->_orderBase );
		unset( $this->_couponItem );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->_object->isAvailable( $this->_orderBase ) );
	}


	public function testIsAvailableWithProduct()
	{
		$this->_couponItem->setConfig( array( 'required.productcode' => 'CNC' ) );

		$this->assertTrue( $this->_object->isAvailable( $this->_orderBase ) );
	}


	public function testIsAvailableWithoutProduct()
	{
		$this->_couponItem->setConfig( array( 'required.productcode' => 'CNE' ) );

		$this->assertFalse( $this->_object->isAvailable( $this->_orderBase ) );
	}

}

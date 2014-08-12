<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Provider_PercentRebate.
 */
class MShop_Coupon_Provider_PercentRebateTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_orderBase;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$couponItem = MShop_Coupon_Manager_Factory::createManager( $context )->createItem();
		$couponItem->setConfig( array( 'percentrebate.productcode' => 'U:MD', 'percentrebate.rebate' => '10' ) );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->_orderBase = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $context->getLocale() );
		$this->_object = new MShop_Coupon_Provider_PercentRebate( $context, $couponItem, 'zyxw' );
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
	}


	public function testAddCoupon()
	{
		$this->_orderBase->addProduct( $this->_getOrderProduct( 'CNE' ) );
		$this->_object->addCoupon( $this->_orderBase );

		$coupons = $this->_orderBase->getCoupons();
		$products = $this->_orderBase->getProducts();

		if( ( $product = reset( $coupons['zyxw'] ) ) === false ) {
			throw new Exception( 'No coupon available' );
		}

		$this->assertEquals( 2, count( $products ) );
		$this->assertEquals( 1, count( $coupons['zyxw'] ) );
		$this->assertEquals( '-1.80', $product->getPrice()->getValue() );
		$this->assertEquals( '1.80', $product->getPrice()->getRebate() );
		$this->assertEquals( 'unitSupplier', $product->getSupplierCode() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
	}


	public function testDeleteCoupon()
	{
		$this->_orderBase->addProduct( $this->_getOrderProduct( 'CNE' ) );

		$this->_object->addCoupon( $this->_orderBase );
		$this->_object->deleteCoupon($this->_orderBase);

		$products = $this->_orderBase->getProducts();
		$coupons = $this->_orderBase->getCoupons();

		$this->assertEquals( 1, count( $products ) );
		$this->assertArrayNotHasKey( 'zyxw', $coupons );
	}


	public function testAddCouponInvalidConfig()
	{
		$context = TestHelper::getContext();
		$couponItem = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() )->createItem();

		$object = new MShop_Coupon_Provider_PercentRebate( $context, $couponItem, 'zyxw' );

		$this->setExpectedException( 'MShop_Coupon_Exception' );
		$object->addCoupon( $this->_orderBase );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->_object->isAvailable( $this->_orderBase ) );
	}


	/**
	 * Return the order product for the given code.
	 *
	 * @param string $code
	 * @param integer $quantity
	 * @return MShop_Order_Item_Base_Product_Interface
	 * @throws Exception
	 */
	protected function _getOrderProduct( $code, $quantity = 1 )
	{
		$context = TestHelper::getContext();

		$priceManager = MShop_Price_Manager_Factory::createManager( $context );
		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$orderProductManager = MShop_Order_Manager_Factory::createManager( $context )
			->getSubManager( 'base' )->getSubManager( 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$result = $productManager->searchItems( $search, array( 'price' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No product with code "%1$s" found', $code ) );
		}

		$priceItems = $item->getRefItems( 'price', 'default', 'default' );

		$orderProductItem = $orderProductManager->createItem();
		$orderProductItem->copyFrom( $item );
		$orderProductItem->setQuantity( $quantity );
		$orderProductItem->setPrice( $priceManager->getLowestPrice( $priceItems, $quantity ) );

		return $orderProductItem;
	}

}

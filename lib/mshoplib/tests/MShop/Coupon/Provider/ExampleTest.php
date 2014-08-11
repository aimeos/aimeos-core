<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Provider_Example.
 */
class MShop_Coupon_Provider_ExampleTest extends MW_Unittest_Testcase
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
		$item = MShop_Coupon_Manager_Factory::createManager( $context )->createItem();

		// Don't create order base item by createItem() as this would already register the plugins
		$this->_orderBase = new MShop_Order_Item_Base_Default( $priceManager->createItem(), $context->getLocale() );
		$this->_object = new MShop_Coupon_Provider_Example( $context, $item, '1234' );
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
		$this->_object->addCoupon( $this->_orderBase );
	}

	public function testDeleteCoupon()
	{
		$this->_object->deleteCoupon( $this->_orderBase );
	}

	public function testUpdateCoupon()
	{
		$this->_object->updateCoupon( $this->_orderBase );
	}
}

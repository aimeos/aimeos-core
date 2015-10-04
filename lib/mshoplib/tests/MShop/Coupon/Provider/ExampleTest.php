<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Coupon_Provider_Example.
 */
class MShop_Coupon_Provider_ExampleTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $orderBase;


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
		$this->orderBase = new MShop_Order_Item_Base_Standard( $priceManager->createItem(), $context->getLocale() );
		$this->object = new MShop_Coupon_Provider_Example( $context, $item, '1234' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testAddCoupon()
	{
		$this->object->addCoupon( $this->orderBase );
	}

	public function testDeleteCoupon()
	{
		$this->object->deleteCoupon( $this->orderBase );
	}

	public function testUpdateCoupon()
	{
		$this->object->updateCoupon( $this->orderBase );
	}

	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}

	public function testSetObject()
	{
		$this->object->setObject( $this->object );
	}
}

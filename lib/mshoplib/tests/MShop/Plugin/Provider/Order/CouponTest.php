<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Plugin_Provider_Order_Complete.
 */
class MShop_Plugin_Provider_Order_CouponTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;


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
		$this->plugin->setProvider( 'Coupon' );
		$this->plugin->setStatus( 1 );

		$priceItem = MShop_Price_Manager_Factory::createManager( $context )->createItem();
		$this->order = new MShop_Order_Item_Base_Default( $priceItem, $context->getLocale() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->plugin );
		unset( $this->order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdate()
	{
		$this->order->addCoupon( 'OPQR', array() );
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'test' ) );
	}


	public function testUpdateInvalidObject()
	{
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->plugin );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$object->update( new MShop_Publisher_Test(), 'test' );
	}
}


class MShop_Publisher_Test extends MW_Observer_Publisher_Abstract
{
}

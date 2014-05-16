<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Plugin_Provider_Order_Complete.
 */
class MShop_Plugin_Provider_Order_CouponTest extends MW_Unittest_Testcase
{
	private $_order;
	private $_plugin;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Provider_Order_CompleteTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

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
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'Coupon' );
		$this->_plugin->setStatus( 1 );

		$priceItem = MShop_Price_Manager_Factory::createManager( $context )->createItem();
		$this->_order = new MShop_Order_Item_Base_Default( $priceItem, $context->getLocale() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_plugin );
		unset( $this->_order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdate()
	{
		$this->_order->addCoupon( 'OPQR', array() );
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->_plugin );

		$this->assertTrue( $object->update( $this->_order, 'test' ) );
	}


	public function testUpdateInvalidObject()
	{
		$object = new MShop_Plugin_Provider_Order_Coupon( TestHelper::getContext(), $this->_plugin );

		$this->setExpectedException( 'MShop_Plugin_Exception' );
		$object->update( new MShop_Publisher_Test(), 'test' );
	}
}


class MShop_Publisher_Test extends MW_Observer_Publisher_Abstract
{
}

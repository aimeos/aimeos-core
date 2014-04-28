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

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager('base');
		$this->_order = $orderBaseManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_order );
		MShop_Factory::clear();
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_Coupon(TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdate()
	{
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}

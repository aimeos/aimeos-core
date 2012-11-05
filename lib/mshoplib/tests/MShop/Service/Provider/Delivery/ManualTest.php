<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ManualTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */


/**
 * Test class for MShop_Service_Provider_Delivery_Manual.
 */
class MShop_Service_Provider_Delivery_ManualTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var    MShop_Service_Provider_Delivery_Manual
	 * @access protected
	 */
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Provider_Delivery_ManualTest');
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

		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );

		$serviceItem = $serviceManager->createItem();

		$this->_object = new MShop_Service_Provider_Delivery_Manual( $context, $serviceItem );
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
	}


	public function testGetConfigBE()
	{
		$this->assertEquals( array(), $this->_object->getConfigBE() );
	}


	public function testGetConfigFE()
	{
		$this->assertEquals( array(), $this->_object->getConfigFE() );
	}
	
	
	public function testProcess()
	{
		$manager = MShop_Order_Manager_Factory::createManager( TestHelper::getContext() );
		$order = $manager->createItem();
		$this->_object->process( $order );

		$this->assertEquals( MShop_Order_Item_Abstract::STAT_PROGRESS, $order->getDeliveryStatus() );
	}

}

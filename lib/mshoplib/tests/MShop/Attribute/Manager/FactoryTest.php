<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: FactoryTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


/**
 * Test class for MShop_Attribute_Manager_Default.
 */
class MShop_Attribute_Manager_FactoryTest extends MW_Unittest_Testcase
{
	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Attribute_Manager_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	/**
	 * testCreateManager().
	 */
	public function testCreateManager()
	{
		$target = 'MShop_Common_Manager_Interface';
		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $manager );

		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $manager );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException('MShop_Attribute_Exception');
		MShop_Attribute_Manager_Factory::createManager(TestHelper::getContext(), '%^');
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException('MShop_Exception');
		MShop_Attribute_Manager_Factory::createManager(TestHelper::getContext(), 'notexist');
	}

}
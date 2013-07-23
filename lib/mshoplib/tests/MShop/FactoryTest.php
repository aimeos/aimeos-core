<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Factory.
 */
class MShop_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateManager()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'attribute' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateSubManager()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'attribute/list/type' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), 'attribute/notexist' );
	}

}
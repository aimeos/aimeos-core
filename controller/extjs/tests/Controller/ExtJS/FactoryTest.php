<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_ExtJS_Factory.
 */
class Controller_ExtJS_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('Controller_ExtJS_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateManager()
	{
		$manager = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Interface', $manager );
	}


	public function testCreateSubManager()
	{
		$manager = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/list/type' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Interface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/notexist' );
	}

}
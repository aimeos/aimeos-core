<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Frontend_Factory.
 */
class Controller_Frontend_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateController()
	{
		$controller = Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket' );
		$this->assertInstanceOf( 'Controller_Frontend_Common_Interface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket/notexist' );
	}

}
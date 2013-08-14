<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Jobs_Factory.
 */
class Controller_Jobs_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Jobs_FactoryTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateController()
	{
		$controller = Controller_Jobs_Factory::createController( TestHelper::getContext(), 'admin/job' );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testGetControllers()
	{
		$list = Controller_Jobs_Factory::getControllers( TestHelper::getContext(), TestHelper::getControllerPaths() );

		$this->assertGreaterThan( 0, count( $list ) );

		foreach( $list as $key => $object ) {
			$this->assertInstanceOf( 'Controller_Jobs_Interface', $object );
		}
	}
}
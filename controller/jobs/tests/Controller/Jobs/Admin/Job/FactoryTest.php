<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Admin_Job_FactoryTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_Jobs_Admin_Job_FactoryTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	public function testCreateController()
	{
		$obj = Controller_Jobs_Admin_Job_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( 'Controller_Jobs_Interface', $obj);
	}


	public function testFactoryExceptionWrongName()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Admin_Job_Factory::createController(TestHelper::getContext(), 'Wrong$$$Name' );
	}


	public function testFactoryExceptionWrongClass()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Admin_Job_Factory::createController(TestHelper::getContext(), 'WrongClass' );
	}


	public function testFactoryExceptionWrongInterface()
	{
		$this->setExpectedException( 'Controller_Jobs_Exception' );
		$object = Controller_Jobs_Admin_Job_Factory::createController(TestHelper::getContext(), 'Factory' );
	}

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: ExampleTest.php 1116 2012-08-13 08:17:32Z nsendetzky $
 */


class Controller_Frontend_Plugin_Decorator_ExampleTest extends MW_Unittest_Testcase
{
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_Frontend_Plugin_Decorator_ExampleTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
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
		$controller = Controller_Frontend_Service_Factory::createController( $context, 'Default' );
		$this->_object = new Controller_Frontend_Service_Decorator_Example( $context, $controller );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCall()
	{
		$this->setExpectedException( 'Controller_Frontend_Service_Exception' );
		$result = $this->_object->checkServiceAttributes( 'delivery', -1, array() );
	}

}

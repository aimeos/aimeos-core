<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Jobs_Common_Factory_AbstractTest.
 */
class Controller_Jobs_Common_Factory_AbstractTest extends MW_Unittest_Testcase
{
	private $_context;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Jobs_Common_Factory_AbstractTest');
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
		$this->_context = TestHelper::getContext();
		$config = $this->_context->getConfig();

		$config->set( 'controller/jobs/common/decorators/default', array() );
		$config->set( 'controller/jobs/admin/decorators/global', array() );
		$config->set( 'controller/jobs/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$controller = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $controller );

		$injectedController = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, 'Default' );

		$this->assertSame( $controller, $injectedController );
	}


	public function testInjectControllerReset()
	{
		$controller = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $controller );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', null );

		$new = Controller_Jobs_Admin_Job_Factory::createController( $this->_context, 'Default' );

		$this->assertNotSame( $controller, $new );
	}

}
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_ExtJS_Common_Factory_AbstractTest.
 */
class Controller_ExtJS_Common_Factory_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/extjs/common/decorators/default', array() );
		$config->set( 'controller/extjs/admin/log/decorators/global', array() );
		$config->set( 'controller/extjs/admin/log/decorators/local', array() );

	}


	public function testInjectController()
	{
		$controller = Controller_ExtJS_Admin_Job_Factory::createController( $this->context, 'Default' );
		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_Default', $controller );

		$injectedController = Controller_ExtJS_Admin_Job_Factory::createController( $this->context, 'Default' );

		$this->assertSame( $controller, $injectedController );
	}


	public function testInjectControllerReset()
	{
		$controller = Controller_ExtJS_Admin_Job_Factory::createController( $this->context, 'Default' );
		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_Default', $controller );
		Controller_ExtJS_Admin_Job_Factory::injectController( 'Controller_ExtJS_Admin_Job_Default', null );

		$new = Controller_ExtJS_Admin_Job_Factory::createController( $this->context, 'Default' );

		$this->assertNotSame( $controller, $new );
	}

}
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_Frontend_Common_Factory_AbstractTest.
 */
class Controller_Frontend_Common_Factory_AbstractTest extends PHPUnit_Framework_TestCase
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

		$config->set( 'controller/frontend/common/decorators/default', array() );
		$config->set( 'controller/frontend/admin/decorators/global', array() );
		$config->set( 'controller/frontend/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$controller = Controller_Frontend_Catalog_Factory::createController( $this->context, 'Default' );
		Controller_Frontend_Catalog_Factory::injectController( 'Controller_Frontend_Catalog_Default', $controller );

		$injectedController = Controller_Frontend_Catalog_Factory::createController( $this->context, 'Default' );

		$this->assertSame( $controller, $injectedController );
	}


	public function testInjectControllerReset()
	{
		$controller = Controller_Frontend_Catalog_Factory::createController( $this->context, 'Default' );
		Controller_Frontend_Catalog_Factory::injectController( 'Controller_Frontend_Catalog_Default', $controller );
		Controller_Frontend_Catalog_Factory::injectController( 'Controller_Frontend_Catalog_Default', null );

		$new = Controller_Frontend_Catalog_Factory::createController( $this->context, 'Default' );

		$this->assertNotSame( $controller, $new );
	}

}
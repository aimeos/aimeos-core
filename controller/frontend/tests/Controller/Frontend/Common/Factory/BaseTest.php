<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\Controller\Frontend\Common\Factory;


/**
 * Test class for \Aimeos\Controller\Frontend\Common\Factory\BaseTest.
 */
class BaseTest extends \PHPUnit_Framework_TestCase
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
		$this->context = \TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array() );
		$config->set( 'controller/frontend/admin/decorators/global', array() );
		$config->set( 'controller/frontend/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$controller = \Aimeos\Controller\Frontend\Catalog\Factory::createController( $this->context, 'Standard' );
		\Aimeos\Controller\Frontend\Catalog\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Catalog\\Standard', $controller );

		$injectedController = \Aimeos\Controller\Frontend\Catalog\Factory::createController( $this->context, 'Standard' );

		$this->assertSame( $controller, $injectedController );
	}


	public function testInjectControllerReset()
	{
		$controller = \Aimeos\Controller\Frontend\Catalog\Factory::createController( $this->context, 'Standard' );
		\Aimeos\Controller\Frontend\Catalog\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Catalog\\Standard', $controller );
		\Aimeos\Controller\Frontend\Catalog\Factory::injectController( '\\Aimeos\\Controller\\Frontend\\Catalog\\Standard', null );

		$new = \Aimeos\Controller\Frontend\Catalog\Factory::createController( $this->context, 'Standard' );

		$this->assertNotSame( $controller, $new );
	}

}
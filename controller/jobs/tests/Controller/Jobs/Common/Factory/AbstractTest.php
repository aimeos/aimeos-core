<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_Jobs_Common_Factory_BaseTest.
 */
class Controller_Jobs_Common_Factory_BaseTest extends PHPUnit_Framework_TestCase
{
	private $context;
	private $aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->aimeos = TestHelper::getAimeos();
		$this->context = TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/jobs/common/decorators/default', array() );
		$config->set( 'controller/jobs/admin/decorators/global', array() );
		$config->set( 'controller/jobs/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $cntl );

		$iCntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->assertSame( $cntl, $iCntl );
	}


	public function testInjectControllerReset()
	{
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', $cntl );
		Controller_Jobs_Admin_Job_Factory::injectController( 'Controller_Jobs_Admin_Job_Default', null );

		$new = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->assertNotSame( $cntl, $new );
	}


	public function testAddDecoratorsInvalidName()
	{
		$decorators = array( '$' );
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Common_Factory_TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl, $decorators, 'Test_' );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$decorators = array( 'Test' );
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Common_Factory_TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl, $decorators, 'Test_' );
	}


	public function testAddDecoratorsInvalidInterface()
	{
		$decorators = array( 'Test' );
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Common_Factory_TestAbstract::addDecoratorsPublic( $this->context, $this->aimeos, $cntl,
			$decorators, 'Controller_Jobs_Common_Decorator_' );
	}


	public function testAddControllerDecoratorsInvalidDomain()
	{
		$cntl = Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Common_Factory_TestAbstract::addControllerDecoratorsPublic( $this->context, $this->aimeos, $cntl, '' );
	}


	public function testAddControllerDecoratorsExcludes()
	{
		$this->context->getConfig()->set( 'controller/jobs/test/decorators/excludes', array( 'test' ) );
		$this->context->getConfig()->set( 'controller/jobs/common/decorators/default', array( 'test' ) );

		$this->setExpectedException( 'Controller_Jobs_Exception' );
		Controller_Jobs_Admin_Job_Factory::createController( $this->context, $this->aimeos, 'Default' );
	}
}


class Controller_Jobs_Common_Factory_TestAbstract
	extends Controller_Jobs_Common_Factory_Base
{
	public static function addDecoratorsPublic( MShop_Context_Item_Interface $context, Aimeos $aimeos,
		Controller_Jobs_Interface $controller, array $decorators, $classprefix )
	{
		self::addDecorators( $context, $aimeos, $controller, $decorators, $classprefix );
	}

	public static function addControllerDecoratorsPublic( MShop_Context_Item_Interface $context, Aimeos $aimeos,
		Controller_Jobs_Interface $controller, $domain )
	{
		self::addControllerDecorators( $context, $aimeos, $controller, $domain );
	}
}


class Controller_Jobs_Common_Decorator_Test
{
}

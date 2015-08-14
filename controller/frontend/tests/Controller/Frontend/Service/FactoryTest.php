<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Frontend_Service_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factory::createController( TestHelper::getContext(), 'Invalid' );
	}

	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factory::createController( TestHelper::getContext(), '%^' );
	}

	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factory::createController( TestHelper::getContext(), 'notexist' );
	}

	public function testAbstractAddControllerDecoratorsWithExclude()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array( 'Example' ) );
		$config->set( 'controller/frontend/service/decorators/excludes', array( 'Example' ) );

		$controller = Controller_Frontend_Service_Factory::createController( $context, 'Default' );

		$this->assertInstanceOf( 'Controller_Frontend_Common_Interface', $controller );
	}

	// using Factorylocal class
	public function testAbstractAddControllerDecoratorsNoDomainException()
	{
		$context = TestHelper::getContext();
		Controller_Frontend_Service_Factorylocal::createController( $context, 'Default', 'service/type' );

		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factorylocal::createController( $context, 'Default', '' );
	}

	public function testAbstractAddDecorators()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array( 'Example', 'Example' ) );
		$config->set( 'controller/frontend/service/decorators/excludes', array() );

		$controller = Controller_Frontend_Service_Factory::createController( $context, 'Default' );

		$this->assertInstanceOf( 'Controller_Frontend_Common_Decorator_Interface', $controller );
	}

	public function testAbstractAddDecoratorsExceptionWrongName()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( '$$' ) );

		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factory::createController( $context, 'Default' );
	}

	public function testAbstractAddDecoratorsExceptionWrongClass()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( 'WrongClass' ) );

		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Service_Factory::createController( $context, 'Default' );
	}

	public function testCreateController()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array() );

		$target = 'Controller_Frontend_Service_Interface';

		$controller = Controller_Frontend_Service_Factory::createController( TestHelper::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = Controller_Frontend_Service_Factory::createController( TestHelper::getContext(), 'Default' );
		$this->assertInstanceOf( $target, $controller );
	}

}

<?php

namespace Aimeos\Controller\Frontend\Service;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelper::getContext(), 'Invalid' );
	}

	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelper::getContext(), '%^' );
	}

	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelper::getContext(), 'notexist' );
	}

	public function testAbstractAddControllerDecoratorsWithExclude()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array( 'Example' ) );
		$config->set( 'controller/frontend/service/decorators/excludes', array( 'Example' ) );

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\Frontend\\Common\\Iface', $controller );
	}

	// using Factorylocal class
	public function testAbstractAddControllerDecoratorsNoDomainException()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factorylocal::createController( \TestHelper::getContext(), 'Standard', '' );
	}

	public function testAbstractAddDecorators()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array( 'Example', 'Example' ) );
		$config->set( 'controller/frontend/service/decorators/excludes', array() );

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\Frontend\\Common\\Decorator\\Iface', $controller );
	}

	public function testAbstractAddDecoratorsExceptionWrongName()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( '$$' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );
	}

	public function testAbstractAddDecoratorsExceptionWrongClass()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( 'WrongClass' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );
	}

	public function testCreateController()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array() );

		$target = '\\Aimeos\\Controller\\Frontend\\Service\\Iface';

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelper::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelper::getContext(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}

}

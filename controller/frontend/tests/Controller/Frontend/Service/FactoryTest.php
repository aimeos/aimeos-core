<?php

namespace Aimeos\Controller\Frontend\Service;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateControllerInvalidImplementation()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelperFrontend::getContext(), 'Invalid' );
	}

	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelperFrontend::getContext(), '%^' );
	}

	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelperFrontend::getContext(), 'notexist' );
	}

	public function testAbstractAddControllerDecoratorsWithExclude()
	{
		$context = \TestHelperFrontend::getContext();
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
		\Aimeos\Controller\Frontend\Service\Factorylocal::createController( \TestHelperFrontend::getContext(), 'Standard', '' );
	}

	public function testAbstractAddDecorators()
	{
		$context = \TestHelperFrontend::getContext();
		$config = $context->getConfig();

		$config->set( 'controller/frontend/common/decorators/default', array( 'Example', 'Example' ) );
		$config->set( 'controller/frontend/service/decorators/excludes', array() );

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );

		$this->assertInstanceOf( '\\Aimeos\\Controller\\Frontend\\Common\\Decorator\\Iface', $controller );
	}

	public function testAbstractAddDecoratorsExceptionWrongName()
	{
		$context = \TestHelperFrontend::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( '$$' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );
	}

	public function testAbstractAddDecoratorsExceptionWrongClass()
	{
		$context = \TestHelperFrontend::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array( 'WrongClass' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\Frontend\\Exception' );
		\Aimeos\Controller\Frontend\Service\Factory::createController( $context, 'Standard' );
	}

	public function testCreateController()
	{
		$context = \TestHelperFrontend::getContext();
		$config = $context->getConfig();
		$config->set( 'controller/frontend/common/decorators/default', array() );

		$target = '\\Aimeos\\Controller\\Frontend\\Service\\Iface';

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelperFrontend::getContext() );
		$this->assertInstanceOf( $target, $controller );

		$controller = \Aimeos\Controller\Frontend\Service\Factory::createController( \TestHelperFrontend::getContext(), 'Standard' );
		$this->assertInstanceOf( $target, $controller );
	}

}

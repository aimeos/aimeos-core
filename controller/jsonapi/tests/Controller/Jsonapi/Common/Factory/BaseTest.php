<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\Jsonapi\Common\Factory;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelper::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/jobs/common/decorators/default', array() );
		$config->set( 'controller/jobs/admin/decorators/global', array() );
		$config->set( 'controller/jobs/admin/decorators/local', array() );

	}


	public function testInjectController()
	{
		$cntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
		\Aimeos\Controller\Jsonapi\Factory::injectController( '\\Aimeos\\Controller\\Jsonapi\\Standard', $cntl );

		$iCntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->assertSame( $cntl, $iCntl );
	}


	public function testInjectControllerReset()
	{
		$cntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
		\Aimeos\Controller\Jsonapi\Factory::injectController( '\\Aimeos\\Controller\\Jsonapi\\Standard', $cntl );
		\Aimeos\Controller\Jsonapi\Factory::injectController( '\\Aimeos\\Controller\\Jsonapi\\Standard', null );

		$new = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->assertNotSame( $cntl, $new );
	}


	public function testAddDecoratorsInvalidName()
	{
		$decorators = array( '$' );
		$cntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jsonapi\\Exception' );
		\Aimeos\Controller\Jsonapi\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators, 'Test', $this->context, array(), 'attribute' );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$decorators = array( 'Test' );
		$cntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jsonapi\\Exception' );
		\Aimeos\Controller\Jsonapi\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators, 'TestDecorator', $this->context, array(), 'attribute' );
	}


	public function testAddDecoratorsInvalidInterface()
	{
		$decorators = array( 'Test' );
		$cntl = \Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jsonapi\\Exception' );
		\Aimeos\Controller\Jsonapi\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators,
			'\\Aimeos\\Controller\\Jobs\\Common\\Decorator\\', $this->context, array(), 'attribute' );
	}


	public function testAddControllerDecoratorsExcludes()
	{
		$this->context->getConfig()->set( 'controller/jsonapi/decorators/excludes', array( 'TestDecorator' ) );
		$this->context->getConfig()->set( 'controller/jsonapi/common/decorators/default', array( 'TestDecorator' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\Jsonapi\\Exception' );
		\Aimeos\Controller\Jsonapi\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
	}
}


class TestAbstract
	extends \Aimeos\Controller\Jsonapi\Common\Factory\Base
{
	public static function addDecoratorsPublic( \Aimeos\Controller\Jsonapi\Iface $controller, array $decorators, $classprefix,
		\Aimeos\MShop\Context\Item\Iface $context, $templatePaths, $path )
	{
		self::addDecorators( $controller, $decorators, $classprefix, $context, $templatePaths, $path );
	}

	public static function addControllerDecoratorsPublic( \Aimeos\Controller\Jsonapi\Iface $controller,
		\Aimeos\MShop\Context\Item\Iface $context, $templatePaths, $path )
	{
		self::addControllerDecorators( $controller, $context, $templatePaths, $path );
	}
}


class TestDecorator
{
}

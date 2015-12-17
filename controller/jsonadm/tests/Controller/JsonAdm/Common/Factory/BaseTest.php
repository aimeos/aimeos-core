<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm\Common\Factory;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperJadm::getContext();
		$config = $this->context->getConfig();

		$config->set( 'controller/jsonadm/common/decorators/default', array() );
		$config->set( 'controller/jsonadm/decorators/global', array() );
		$config->set( 'controller/jsonadm/decorators/local', array() );

	}


	public function testInjectController()
	{
		$cntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
		\Aimeos\Controller\JsonAdm\Factory::injectController( '\\Aimeos\\Controller\\JsonAdm\\Standard', $cntl );

		$iCntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->assertSame( $cntl, $iCntl );
	}


	public function testInjectControllerReset()
	{
		$cntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
		\Aimeos\Controller\JsonAdm\Factory::injectController( '\\Aimeos\\Controller\\JsonAdm\\Standard', $cntl );
		\Aimeos\Controller\JsonAdm\Factory::injectController( '\\Aimeos\\Controller\\JsonAdm\\Standard', null );

		$new = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->assertNotSame( $cntl, $new );
	}


	public function testAddDecoratorsInvalidName()
	{
		$decorators = array( '$' );
		$view = $this->context->getView();
		$cntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators, 'Test', $this->context, $view, array(), 'attribute' );
	}


	public function testAddDecoratorsInvalidClass()
	{
		$decorators = array( 'Test' );
		$view = $this->context->getView();
		$cntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators, 'TestDecorator', $this->context, $view, array(), 'attribute' );
	}


	public function testAddDecoratorsInvalidInterface()
	{
		$decorators = array( 'Test' );
		$view = $this->context->getView();
		$cntl = \Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Common\Factory\TestAbstract::addDecoratorsPublic( $cntl, $decorators,
			'\\Aimeos\\Controller\\Jobs\\Common\\Decorator\\', $this->context, $view, array(), 'attribute' );
	}


	public function testAddControllerDecoratorsExcludes()
	{
		$this->context->getConfig()->set( 'controller/jsonadm/decorators/excludes', array( 'TestDecorator' ) );
		$this->context->getConfig()->set( 'controller/jsonadm/common/decorators/default', array( 'TestDecorator' ) );

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Factory::createController( $this->context, array(), 'attribute', 'Standard' );
	}
}


class TestAbstract
	extends \Aimeos\Controller\JsonAdm\Common\Factory\Base
{
	public static function addDecoratorsPublic( \Aimeos\Controller\JsonAdm\Iface $controller, array $decorators, $classprefix,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, $templatePaths, $path )
	{
		self::addDecorators( $controller, $decorators, $classprefix, $context, $view, $templatePaths, $path );
	}

	public static function addControllerDecoratorsPublic( \Aimeos\Controller\JsonAdm\Iface $controller,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, $templatePaths, $path )
	{
		self::addControllerDecorators( $controller, $view, $context, $templatePaths, $path );
	}
}


class TestDecorator
{
}

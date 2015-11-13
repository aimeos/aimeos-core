<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\JsonAdm;


class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$controller = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\JsonAdm\\Common\\Iface', $controller );
	}


	public function testCreateSubController()
	{
		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$controller = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute/lists/type' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\JsonAdm\\Common\\Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$this->setExpectedException( '\\Aimeos\\Controller\\JsonAdm\\Exception' );
		\Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, '%^' );
	}


	public function testClear()
	{
		$cache = \Aimeos\Controller\JsonAdm\Factory::setCache( true );

		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$controller1 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		\Aimeos\Controller\JsonAdm\Factory::clear();
		$controller2 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );

		\Aimeos\Controller\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\Controller\JsonAdm\Factory::setCache( true );

		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$cntlA1 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		$cntlB1 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute/lists/type' );
		\Aimeos\Controller\JsonAdm\Factory::clear( (string) $context );

		$cntlA2 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		$cntlB2 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute/lists/type' );

		\Aimeos\Controller\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\Controller\JsonAdm\Factory::setCache( true );

		$context = \TestHelper::getContext();
		$templatePaths = \TestHelper::getControllerPaths();

		$cntlA1 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		$cntlB1 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute/lists/type' );

		\Aimeos\Controller\JsonAdm\Factory::clear( (string) $context, 'attribute' );

		$cntlA2 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute' );
		$cntlB2 = \Aimeos\Controller\JsonAdm\Factory::createController( $context, $templatePaths, 'attribute/lists/type' );

		\Aimeos\Controller\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}
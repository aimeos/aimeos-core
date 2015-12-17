<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Controller\ExtJS;


/**
 * Test class for \Aimeos\Controller\ExtJS\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$controller = \Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), 'attribute' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Common\\Iface', $controller );
	}


	public function testCreateSubController()
	{
		$controller = \Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), 'attribute/lists/type' );
		$this->assertInstanceOf( '\\Aimeos\\Controller\\ExtJS\\Common\\Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\Controller\\ExtJS\\Exception' );
		\Aimeos\Controller\ExtJS\Factory::createController( \TestHelperExtjs::getContext(), 'attribute/notexist' );
	}


	public function testClear()
	{
		$cache = \Aimeos\Controller\ExtJS\Factory::setCache( true );

		$context = \TestHelperExtjs::getContext();

		$controller1 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );
		\Aimeos\Controller\ExtJS\Factory::clear();
		$controller2 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );

		\Aimeos\Controller\ExtJS\Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\Controller\ExtJS\Factory::setCache( true );

		$context = \TestHelperExtjs::getContext();

		$cntlA1 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );
		$cntlB1 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute/lists/type' );
		\Aimeos\Controller\ExtJS\Factory::clear( (string) $context );

		$cntlA2 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );
		$cntlB2 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute/lists/type' );

		\Aimeos\Controller\ExtJS\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\Controller\ExtJS\Factory::setCache( true );

		$context = \TestHelperExtjs::getContext();

		$cntlA1 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );
		$cntlB1 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute/lists/type' );
		\Aimeos\Controller\ExtJS\Factory::clear( (string) $context, 'attribute' );

		$cntlA2 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute' );
		$cntlB2 = \Aimeos\Controller\ExtJS\Factory::createController( $context, 'attribute/lists/type' );

		\Aimeos\Controller\ExtJS\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}
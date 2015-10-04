<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for Controller_ExtJS_Factory.
 */
class Controller_ExtJS_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateController()
	{
		$controller = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Iface', $controller );
	}


	public function testCreateSubController()
	{
		$controller = Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/lists/type' );
		$this->assertInstanceOf( 'Controller_ExtJS_Common_Iface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		Controller_ExtJS_Factory::createController( TestHelper::getContext(), 'attribute/notexist' );
	}


	public function testClear()
	{
		$cache = Controller_ExtJS_Factory::setCache( true );

		$context = TestHelper::getContext();

		$controller1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		Controller_ExtJS_Factory::clear();
		$controller2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );

		Controller_ExtJS_Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = Controller_ExtJS_Factory::setCache( true );

		$context = TestHelper::getContext();

		$cntlA1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB1 = Controller_ExtJS_Factory::createController( $context, 'attribute/lists/type' );
		Controller_ExtJS_Factory::clear( (string) $context );

		$cntlA2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB2 = Controller_ExtJS_Factory::createController( $context, 'attribute/lists/type' );

		Controller_ExtJS_Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$cache = Controller_ExtJS_Factory::setCache( true );

		$context = TestHelper::getContext();

		$cntlA1 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB1 = Controller_ExtJS_Factory::createController( $context, 'attribute/lists/type' );
		Controller_ExtJS_Factory::clear( (string) $context, 'attribute' );

		$cntlA2 = Controller_ExtJS_Factory::createController( $context, 'attribute' );
		$cntlB2 = Controller_ExtJS_Factory::createController( $context, 'attribute/lists/type' );

		Controller_ExtJS_Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}
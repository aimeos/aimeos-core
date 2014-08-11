<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for Controller_Frontend_Factory.
 */
class Controller_Frontend_FactoryTest extends MW_Unittest_Testcase
{
	public function testCreateController()
	{
		$controller = Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket' );
		$this->assertInstanceOf( 'Controller_Frontend_Common_Interface', $controller );
	}


	public function testCreateControllerEmpty()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), "\t\n" );
	}


	public function testCreateControllerInvalidName()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), '%^' );
	}


	public function testCreateControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'notexist' );
	}


	public function testCreateSubControllerNotExisting()
	{
		$this->setExpectedException( 'Controller_Frontend_Exception' );
		Controller_Frontend_Factory::createController( TestHelper::getContext(), 'basket/notexist' );
	}


	public function testClear()
	{
		$cache = Controller_Frontend_Factory::setCache( true );

		$context = TestHelper::getContext();

		$controller1 = Controller_Frontend_Factory::createController( $context, 'basket' );
		Controller_Frontend_Factory::clear();
		$controller2 = Controller_Frontend_Factory::createController( $context, 'basket' );

		Controller_Frontend_Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = Controller_Frontend_Factory::setCache( true );

		$context = TestHelper::getContext();

		$basket1 = Controller_Frontend_Factory::createController( $context, 'basket' );
		$catalog1 = Controller_Frontend_Factory::createController( $context, 'catalog' );
		Controller_Frontend_Factory::clear( (string) $context );

		$basket2 = Controller_Frontend_Factory::createController( $context, 'basket' );
		$catalog2 = Controller_Frontend_Factory::createController( $context, 'catalog' );

		Controller_Frontend_Factory::setCache( $cache );

		$this->assertNotSame( $basket1, $basket2 );
		$this->assertNotSame( $catalog1, $catalog2 );
	}


	public function testClearSpecific()
	{
		$cache = Controller_Frontend_Factory::setCache( true );

		$context = TestHelper::getContext();

		$basket1 = Controller_Frontend_Factory::createController( $context, 'basket' );
		$catalog1 = Controller_Frontend_Factory::createController( $context, 'catalog' );
		Controller_Frontend_Factory::clear( (string) $context, 'basket' );

		$basket2 = Controller_Frontend_Factory::createController( $context, 'basket' );
		$catalog2 = Controller_Frontend_Factory::createController( $context, 'catalog' );

		Controller_Frontend_Factory::setCache( $cache );

		$this->assertNotSame( $basket1, $basket2 );
		$this->assertSame( $catalog1, $catalog2 );
	}

}
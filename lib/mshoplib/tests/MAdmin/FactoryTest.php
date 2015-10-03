<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MAdmin_Factory.
 */
class MAdmin_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = MAdmin_Factory::createManager( TestHelper::getContext(), 'job' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		MAdmin_Factory::createManager( TestHelper::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		MAdmin_Factory::createManager( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		MAdmin_Factory::createManager( TestHelper::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( 'MAdmin_Exception' );
		MAdmin_Factory::createManager( TestHelper::getContext(), 'job/unknown' );
	}


	public function testClear()
	{
		$cache = MAdmin_Factory::setCache( true );

		$context = TestHelper::getContext();

		$controller1 = MAdmin_Factory::createManager( $context, 'log' );
		MAdmin_Factory::clear();
		$controller2 = MAdmin_Factory::createManager( $context, 'log' );

		MAdmin_Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = MAdmin_Factory::setCache( true );

		$context = TestHelper::getContext();

		$managerA1 = MAdmin_Factory::createManager( $context, 'log' );
		MAdmin_Factory::clear( (string) $context );
		$managerA2 = MAdmin_Factory::createManager( $context, 'log' );

		MAdmin_Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
	}


	public function testClearSpecific()
	{
		$cache = MAdmin_Factory::setCache( true );

		$context = TestHelper::getContext();

		$managerA1 = MAdmin_Factory::createManager( $context, 'log' );
		$managerB1 = MAdmin_Factory::createManager( $context, 'job' );

		MAdmin_Factory::clear( (string) $context, 'log' );

		$managerA2 = MAdmin_Factory::createManager( $context, 'log' );
		$managerB2 = MAdmin_Factory::createManager( $context, 'job' );

		MAdmin_Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertSame( $managerB1, $managerB2 );
	}

}
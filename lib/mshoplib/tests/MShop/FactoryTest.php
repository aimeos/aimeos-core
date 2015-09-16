<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Factory.
 */
class MShop_FactoryTest extends PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'attribute' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateSubManager()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'attribute/list/type' );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( 'MShop_Exception' );
		MShop_Factory::createManager( TestHelper::getContext(), 'attribute/unknown' );
	}


	public function testClear()
	{
		$cache = MShop_Factory::setCache( true );

		$context = TestHelper::getContext();

		$controller1 = MShop_Factory::createManager( $context, 'attribute' );
		MShop_Factory::clear();
		$controller2 = MShop_Factory::createManager( $context, 'attribute' );

		MShop_Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = MShop_Factory::setCache( true );

		$context = TestHelper::getContext();

		$managerA1 = MShop_Factory::createManager( $context, 'attribute' );
		$managerB1 = MShop_Factory::createManager( $context, 'attribute/list/type' );
		MShop_Factory::clear( (string) $context );

		$managerA2 = MShop_Factory::createManager( $context, 'attribute' );
		$managerB2 = MShop_Factory::createManager( $context, 'attribute/list/type' );

		MShop_Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertNotSame( $managerB1, $managerB2 );
	}


	public function testClearSpecific()
	{
		$cache = MShop_Factory::setCache( true );

		$context = TestHelper::getContext();

		$managerA1 = MShop_Factory::createManager( $context, 'attribute' );
		$managerB1 = MShop_Factory::createManager( $context, 'attribute/list/type' );
		MShop_Factory::clear( (string) $context, 'attribute' );

		$managerA2 = MShop_Factory::createManager( $context, 'attribute' );
		$managerB2 = MShop_Factory::createManager( $context, 'attribute/list/type' );

		MShop_Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertSame( $managerB1, $managerB2 );
	}

}
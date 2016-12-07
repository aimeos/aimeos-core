<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop;


/**
 * Test class for \Aimeos\MShop\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'attribute' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateSubManager()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'attribute/lists/type' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		\Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'attribute/unknown' );
	}


	public function testClear()
	{
		$cache = \Aimeos\MShop\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$controller1 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		\Aimeos\MShop\Factory::clear();
		$controller2 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );

		\Aimeos\MShop\Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\MShop\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$managerB1 = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists/type' );
		\Aimeos\MShop\Factory::clear( (string) $context );

		$managerA2 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$managerB2 = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists/type' );

		\Aimeos\MShop\Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertNotSame( $managerB1, $managerB2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\MShop\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$managerB1 = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists/type' );
		\Aimeos\MShop\Factory::clear( (string) $context, 'attribute' );

		$managerA2 = \Aimeos\MShop\Factory::createManager( $context, 'attribute' );
		$managerB2 = \Aimeos\MShop\Factory::createManager( $context, 'attribute/lists/type' );

		\Aimeos\MShop\Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertSame( $managerB1, $managerB2 );
	}

}
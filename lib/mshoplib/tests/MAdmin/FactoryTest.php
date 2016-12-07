<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MAdmin;


/**
 * Test class for \Aimeos\MAdmin\Factory.
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), 'job' );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), 'unknown' );
	}


	public function testCreateSubManagerNotExisting()
	{
		$this->setExpectedException( '\\Aimeos\\MAdmin\\Exception' );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), 'job/unknown' );
	}


	public function testClear()
	{
		$cache = \Aimeos\MAdmin\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$controller1 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );
		\Aimeos\MAdmin\Factory::clear();
		$controller2 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );

		\Aimeos\MAdmin\Factory::setCache( $cache );

		$this->assertNotSame( $controller1, $controller2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\MAdmin\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );
		\Aimeos\MAdmin\Factory::clear( (string) $context );
		$managerA2 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );

		\Aimeos\MAdmin\Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\MAdmin\Factory::setCache( true );

		$context = \TestHelperMShop::getContext();

		$managerA1 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );
		$managerB1 = \Aimeos\MAdmin\Factory::createManager( $context, 'job' );

		\Aimeos\MAdmin\Factory::clear( (string) $context, 'log' );

		$managerA2 = \Aimeos\MAdmin\Factory::createManager( $context, 'log' );
		$managerB2 = \Aimeos\MAdmin\Factory::createManager( $context, 'job' );

		\Aimeos\MAdmin\Factory::setCache( $cache );

		$this->assertNotSame( $managerA1, $managerA2 );
		$this->assertSame( $managerB1, $managerB2 );
	}

}
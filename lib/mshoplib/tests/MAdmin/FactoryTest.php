<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MAdmin;


/**
 * Test class for \Aimeos\MAdmin\Factory.
 */
class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreateManager()
	{
		$manager = \Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), 'job' );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $manager );
	}


	public function testCreateManagerEmpty()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), "\n" );
	}


	public function testCreateManagerInvalidName()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), '%^' );
	}


	public function testCreateManagerNotExisting()
	{
		$this->setExpectedException( \Aimeos\MAdmin\Exception::class );
		\Aimeos\MAdmin\Factory::createManager( \TestHelperMShop::getContext(), 'unknown' );
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
}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\Admin\JsonAdm;


class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateClient()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
	}


	public function testCreateSubClient()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
	}


	public function testCreateClientEmpty()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, '' );
		$this->assertInstanceOf( '\\Aimeos\\Admin\\JsonAdm\\Common\\Iface', $client );
	}


	public function testCreateClientInvalidName()
	{
		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$this->setExpectedException( '\\Aimeos\\Admin\\JsonAdm\\Exception' );
		\Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, '%^' );
	}


	public function testClear()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$client1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		\Aimeos\Admin\JsonAdm\Factory::clear();
		$client2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $client1, $client2 );
	}


	public function testClearSite()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$cntlA1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );
		\Aimeos\Admin\JsonAdm\Factory::clear( (string) $context );

		$cntlA2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertNotSame( $cntlB1, $cntlB2 );
	}


	public function testClearSpecific()
	{
		$cache = \Aimeos\Admin\JsonAdm\Factory::setCache( true );

		$context = \TestHelperJadm::getContext();
		$templatePaths = \TestHelperJadm::getJsonadmPaths();

		$cntlA1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB1 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::clear( (string) $context, 'order' );

		$cntlA2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order' );
		$cntlB2 = \Aimeos\Admin\JsonAdm\Factory::createClient( $context, $templatePaths, 'order/base' );

		\Aimeos\Admin\JsonAdm\Factory::setCache( $cache );

		$this->assertNotSame( $cntlA1, $cntlA2 );
		$this->assertSame( $cntlB1, $cntlB2 );
	}

}
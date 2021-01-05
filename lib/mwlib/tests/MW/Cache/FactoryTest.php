<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Cache;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testFactory()
	{
		$config = array(
			'sql' => array(
				'delete' => '', 'deletebytag' => '',
				'get' => '', 'getbytag' => '',
				'set' => '', 'settag' => ''
			),
			'search' => array(
				'cache.id' => '', 'cache.siteid' => '', 'cache.value' => '',
				'cache.expire' => '', 'cache.tag.name' => ''
			),
		);

		$object = \Aimeos\MW\Cache\Factory::create( 'DB', $config, \TestHelperMw::getDBManager() );
		$this->assertInstanceOf( \Aimeos\MW\Cache\Iface::class, $object );
	}


	public function testFactoryUnknown()
	{
		$this->expectException( \Aimeos\MW\Cache\Exception::class );
		\Aimeos\MW\Cache\Factory::create( 'unknown' );
	}


	public function testFactoryInvalidCharacters()
	{
		$this->expectException( \Aimeos\MW\Cache\Exception::class );
		\Aimeos\MW\Cache\Factory::create( '$$$' );
	}


	public function testFactoryInvalidClass()
	{
		$this->expectException( \Aimeos\MW\Cache\Exception::class );
		\Aimeos\MW\Cache\Factory::create( 'InvalidCache' );
	}
}


class InvalidCache
{
}

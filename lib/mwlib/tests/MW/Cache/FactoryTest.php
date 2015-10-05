<?php

namespace Aimeos\MW\Cache;


/**
 * Test class for \Aimeos\MW\Cache\Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
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

		$object = \Aimeos\MW\Cache\Factory::createManager( 'DB', $config, \TestHelper::getDBManager() );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Cache\\Iface', $object );
	}


	public function testFactoryUnknown()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		\Aimeos\MW\Cache\Factory::createManager( 'unknown', array(), null );
	}


	public function testFactoryInvalidCharacters()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		\Aimeos\MW\Cache\Factory::createManager( '$$$', array(), null );
	}


	public function testFactoryInvalidClass()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Cache\\Exception' );
		\Aimeos\MW\Cache\Factory::createManager( 'InvalidCache', array(), null );
	}
}


class InvalidCache
{
}

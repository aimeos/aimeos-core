<?php

class MW_Cache_Invalid
{
}


/**
 * Test class for MW_Cache_Factory.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Cache_FactoryTest extends PHPUnit_Framework_TestCase
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

		$object = MW_Cache_Factory::createManager( 'DB', $config, TestHelper::getDBManager() );
		$this->assertInstanceOf( 'MW_Cache_Interface', $object );
	}


	public function testFactoryUnknown()
	{
		$this->setExpectedException( 'MW_Cache_Exception' );
		MW_Cache_Factory::createManager( 'unknown', array(), null );
	}


	public function testFactoryInvalidCharacters()
	{
		$this->setExpectedException( 'MW_Cache_Exception' );
		MW_Cache_Factory::createManager( '$$$', array(), null );
	}


	public function testFactoryInvalidClass()
	{
		$this->setExpectedException( 'MW_Cache_Exception' );
		MW_Cache_Factory::createManager( 'Invalid', array(), null );
	}
}

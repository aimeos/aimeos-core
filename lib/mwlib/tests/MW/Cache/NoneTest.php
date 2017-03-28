<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Cache;


class NoneTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MW\Cache\None();
	}


	public function tearDown()
	{
		unset( $this->object );
	}


	public function testDelete()
	{
		$this->object->delete( 'test' );
	}


	public function testDeleteMultiple()
	{
		$this->object->deleteMultiple( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->object->deleteByTags( array( 'test' ) );
	}


	public function testClear()
	{
		$this->object->clear();
	}


	public function testGet()
	{
		$this->assertEquals( 'default', $this->object->get( 'test', 'default' ) );
	}


	public function testGetMultiple()
	{
		$this->assertEquals( array( 'test' => null ), $this->object->getMultiple( array( 'test' ) ) );
	}


	public function testGetMultipleByTags()
	{
		$this->assertEquals( [], $this->object->getMultipleByTags( array( 'test' ) ) );
	}


	public function testSet()
	{
		$this->object->set( 'test', 'testval' );
	}


	public function testSetMultiple()
	{
		$this->object->setMultiple( [] );
	}
}

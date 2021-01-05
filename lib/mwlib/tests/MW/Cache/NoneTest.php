<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Cache;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Cache\None();
	}


	public function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertTrue( $this->object->clear() );
	}


	public function testDelete()
	{
		$this->assertTrue( $this->object->delete( 'test' ) );
	}


	public function testDeleteMultiple()
	{
		$this->assertTrue( $this->object->deleteMultiple( array( 'test' ) ) );
	}


	public function testDeleteByTags()
	{
		$this->assertTrue( $this->object->deleteByTags( array( 'test' ) ) );
	}


	public function testGet()
	{
		$this->assertEquals( 'default', $this->object->get( 'test', 'default' ) );
	}


	public function testGetMultiple()
	{
		$this->assertEquals( array( 'test' => null ), $this->object->getMultiple( array( 'test' ) ) );
	}


	public function testHas()
	{
		$this->assertFalse( $this->object->has( 'test' ) );
	}


	public function testSet()
	{
		$this->assertTrue( $this->object->set( 'test', 'testval' ) );
	}


	public function testSetMultiple()
	{
		$this->assertTrue( $this->object->setMultiple( [] ) );
	}
}

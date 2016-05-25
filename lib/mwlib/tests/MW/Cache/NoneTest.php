<?php

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


	public function testDeleteList()
	{
		$this->object->deleteList( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->object->deleteByTags( array( 'test' ) );
	}


	public function testFlush()
	{
		$this->object->flush();
	}


	public function testGet()
	{
		$this->assertEquals( 'default', $this->object->get( 'test', 'default' ) );
	}


	public function testGetList()
	{
		$this->assertEquals( array(), $this->object->getList( array( 'test' ) ) );
	}


	public function testGetListByTags()
	{
		$this->assertEquals( array(), $this->object->getListByTags( array( 'test' ) ) );
	}


	public function testSet()
	{
		$this->object->set( 'test', 'testval' );
	}


	public function testSetList()
	{
		$this->object->setList( array() );
	}
}

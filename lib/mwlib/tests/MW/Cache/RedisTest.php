<?php

/**
 * Test class for MW_Cache_Redis.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Cache_RedisTest extends MW_Unittest_Testcase
{
	private $_mock;
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$methods = array(
			'del', 'execute', 'expireat', 'flushdb',
			'get', 'mget', 'mset', 'pipeline',
			'sadd', 'set', 'smembers'
		);

		$this->_mock = $this->getMock( 'Predis\Client', $methods );
		$this->_object = new MW_Cache_Redis( array(), $this->_mock );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_mock );
	}


	public function testDelete()
	{
		$this->_mock->expects( $this->once() )->method( 'del' )
			->with( $this->equalTo( 'test' ) );

		$this->_object->delete( 'test' );
	}


	public function testDeleteList()
	{
		$this->_mock->expects( $this->once() )->method( 'del' )
			->with( $this->equalTo( array( 'test' ) ) );

		$this->_object->delete( array( 'test' ) );
	}


	public function testDeleteByTags()
	{
		$this->_mock->expects( $this->once() )->method( 'pipeline' )
			->will( $this->returnValue( $this->_mock ) );

		$this->_mock->expects( $this->exactly( 2 ) )->method( 'smembers' );

		$this->_mock->expects( $this->once() )->method( 'execute' )
			->will( $this->returnValue( array( 'tag:1' => array( 't:1', 't:2' ) ) ) );

		$this->_mock->expects( $this->once() )->method( 'del' )
			->with( $this->equalTo( array( 't:1', 't:2' ) ) );

		$this->_object->deleteByTags( array( 'tag1', 'tag2' ) );
	}


	public function testFlush()
	{
		$this->_mock->expects( $this->once() )->method( 'flushdb' );
		$this->_object->flush();
	}


	public function testGet()
	{
		$this->_mock->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->_object->get( 't:1' ) );
	}


	public function testGetDefault()
	{
		$this->_mock->expects( $this->once() )->method( 'get' );

		$this->assertFalse( $this->_object->get( 't:1', false ) );
	}


	public function testGetExpired()
	{
		$this->_mock->expects( $this->once() )->method( 'get' );

		$this->assertEquals( null, $this->_object->get( 't:1' ) );
	}


	public function testGetList()
	{
		$this->_mock->expects( $this->once() )->method( 'mget' )
			->will( $this->returnValue( array( 0 => 'test' ) ) );

		$this->assertEquals( array( 't:1' => 'test' ), $this->_object->getList( array( 't:1' ) ) );
	}


	public function testGetListByTags()
	{
		$this->_mock->expects( $this->once() )->method( 'pipeline' )
			->will( $this->returnValue( $this->_mock ) );

		$this->_mock->expects( $this->exactly( 2 ) )->method( 'smembers' );

		$this->_mock->expects( $this->once() )->method( 'execute' )
			->will( $this->returnValue( array( 'tag:1' => array( 't:1', 't:2' ) ) ) );

		$this->_mock->expects( $this->once() )->method( 'mget' )
			->will( $this->returnValue( array( 0 => 'test1', 1 => 'test2' ) ) );

		$expected = array( 't:1' => 'test1', 't:2' => 'test2' );
		$result = $this->_object->getListByTags( array( 'tag1', 'tag2' ) );

		$this->assertEquals( $expected, $result );
	}


	public function testSet()
	{
		$this->_mock->expects( $this->once() )->method( 'pipeline' )
			->will( $this->returnValue( $this->_mock ) );

		$this->_mock->expects( $this->once() )->method( 'set' )
			->with( $this->equalTo( 't:1' ), $this->equalTo( 'test 1' ) );

		$this->_mock->expects( $this->exactly( 2 ) )->method( 'sadd' );

		$this->_mock->expects( $this->once() )->method( 'execute' );

		$this->_mock->expects( $this->once() )->method( 'expireat' )
			->with( $this->equalTo( 't:1' ), $this->greaterThan( 0 ) );

		$this->_object->set( 't:1', 'test 1', array( 'tag1', 'tag2' ), '2000-01-01 00:00:00' );
	}


	public function testSetList()
	{
		$this->_mock->expects( $this->once() )->method( 'pipeline' )
			->will( $this->returnValue( $this->_mock ) );

		$this->_mock->expects( $this->once() )->method( 'mset' )
			->with( $this->equalTo( array( 't:1' => 'test 1' ) ) );

		$this->_mock->expects( $this->exactly( 2 ) )->method( 'sadd' );

		$this->_mock->expects( $this->once() )->method( 'execute' );

		$this->_mock->expects( $this->once() )->method( 'expireat' )
			->with( $this->equalTo( 't:1' ), $this->greaterThan( 0 ) );

		$this->_object->setList( array( 't:1' => 'test 1' ), array( 'tag1', 'tag2' ), array( 't:1' => '2000-01-01 00:00:00' ) );
	}
}

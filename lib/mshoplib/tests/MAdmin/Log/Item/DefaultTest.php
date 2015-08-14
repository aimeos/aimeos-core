<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MAdmin_Log_Item_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
			'id' => 1,
			'siteid' => 2,
			'facility' => 'UT facility',
			'timestamp' => '2010-01-01 00:00:00',
			'priority' => 1,
			'message' => 'unittest log message',
			'request' => 'UT request',
			'ctime' => '2010-01-01 00:00:00',
			'mtime' => '2010-01-01 00:00:00',
			'editor' => 'editor foo'
		);

		$this->_object = new MAdmin_Log_Item_Default( $this->_values );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );

		$this->_object->setId( 1 );
		$this->assertEquals( 1, $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setId( 6 );
	}


	public function testSetId2()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setId( 'test' );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 2, $this->_object->getSiteId() );
	}


	public function testGetFacility()
	{
		$this->assertEquals( 'UT facility', $this->_object->getFacility() );
	}


	public function testSetFacility()
	{
		$this->_object->setFacility( 'UT facility' );
		$this->assertEquals( 'UT facility', $this->_object->getFacility() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetPriority()
	{
		$this->assertEquals( 1, $this->_object->getPriority() );
	}


	public function testSetPriority()
	{
		$this->_object->setPriority( 1 );
		$this->assertEquals( 1, $this->_object->getPriority() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetMessage()
	{
		$this->assertEquals( 'unittest log message', $this->_object->getMessage() );
	}


	public function testSetMessage()
	{
		$this->_object->setMessage( 'unittest log message' );
		$this->assertEquals( 'unittest log message', $this->_object->getMessage() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetRequest()
	{
		$this->assertEquals( 'UT request', $this->_object->getRequest() );
	}


	public function testSetRequest()
	{
		$this->_object->setRequest( 'UT request' );
		$this->assertEquals( 'UT request', $this->_object->getRequest() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetTimestamp()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->_object->getTimestamp() );
	}


	public function testFromArray()
	{
		$item = new MAdmin_Log_Item_Default();

		$list = array(
			'log.id' => 1,
			'log.siteid' => 2,
			'log.priority' => 1,
			'log.facility' => 'UT facility',
			'log.timestamp' => '2010-01-01 00:00:00',
			'log.message' => 'unittest log message',
			'log.request' => 'UT request',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array( 'log.timestamp' => '2010-01-01 00:00:00' ), $unknown );

		$this->assertEquals( $list['log.id'], $item->getId() );
		$this->assertEquals( $list['log.priority'], $item->getPriority() );
		$this->assertEquals( $list['log.facility'], $item->getFacility() );
		$this->assertEquals( $list['log.message'], $item->getMessage() );
		$this->assertEquals( $list['log.request'], $item->getRequest() );
		$this->assertNull( $item->getSiteId() );
	}


	public function testToArray()
	{
		$list = $this->_object->toArray();

		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( 1, $list['log.id'] );
		$this->assertEquals( 2, $list['log.siteid'] );
		$this->assertEquals( 'UT facility', $list['log.facility'] );
		$this->assertEquals( '2010-01-01 00:00:00', $list['log.timestamp'] );
		$this->assertEquals( 1, $list['log.priority'] );
		$this->assertEquals( 'unittest log message', $list['log.message'] );
		$this->assertEquals( 'UT request', $list['log.request'] );
	}
}

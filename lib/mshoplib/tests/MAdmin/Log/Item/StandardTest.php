<?php

namespace Aimeos\MAdmin\Log\Item;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'log.id' => 1,
			'log.siteid' => 2,
			'log.facility' => 'UT facility',
			'log.timestamp' => '2010-01-01 00:00:00',
			'log.priority' => 1,
			'log.message' => 'unittest log message',
			'log.request' => 'UT request',
			'log.ctime' => '2010-01-01 00:00:00',
			'log.mtime' => '2010-01-01 00:00:00',
			'log.editor' => 'editor foo'
		);

		$this->object = new \Aimeos\MAdmin\Log\Item\Standard( $this->values );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 1 );
		$this->assertEquals( 1, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 6 );
	}


	public function testSetId2()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 'test' );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 2, $this->object->getSiteId() );
	}


	public function testGetFacility()
	{
		$this->assertEquals( 'UT facility', $this->object->getFacility() );
	}


	public function testSetFacility()
	{
		$this->object->setFacility( 'UT facility' );
		$this->assertEquals( 'UT facility', $this->object->getFacility() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPriority()
	{
		$this->assertEquals( 1, $this->object->getPriority() );
	}


	public function testSetPriority()
	{
		$this->object->setPriority( 1 );
		$this->assertEquals( 1, $this->object->getPriority() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetMessage()
	{
		$this->assertEquals( 'unittest log message', $this->object->getMessage() );
	}


	public function testSetMessage()
	{
		$this->object->setMessage( 'unittest log message' );
		$this->assertEquals( 'unittest log message', $this->object->getMessage() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRequest()
	{
		$this->assertEquals( 'UT request', $this->object->getRequest() );
	}


	public function testSetRequest()
	{
		$this->object->setRequest( 'UT request' );
		$this->assertEquals( 'UT request', $this->object->getRequest() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimestamp()
	{
		$this->assertEquals( '2010-01-01 00:00:00', $this->object->getTimestamp() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'log', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MAdmin\Log\Item\Standard();

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
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( 1, $list['log.id'] );
		$this->assertEquals( 2, $list['log.siteid'] );
		$this->assertEquals( 'UT facility', $list['log.facility'] );
		$this->assertEquals( '2010-01-01 00:00:00', $list['log.timestamp'] );
		$this->assertEquals( 1, $list['log.priority'] );
		$this->assertEquals( 'unittest log message', $list['log.message'] );
		$this->assertEquals( 'UT request', $list['log.request'] );
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MAdmin\Log\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
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


	protected function tearDown() : void
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
		$this->object->setFacility( 'UT facility 2' );
		$this->assertEquals( 'UT facility 2', $this->object->getFacility() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPriority()
	{
		$this->assertEquals( 1, $this->object->getPriority() );
	}


	public function testSetPriority()
	{
		$this->object->setPriority( 2 );
		$this->assertEquals( 2, $this->object->getPriority() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetMessage()
	{
		$this->assertEquals( 'unittest log message', $this->object->getMessage() );
	}


	public function testSetMessage()
	{
		$this->object->setMessage( 'unittest log message 2' );
		$this->assertEquals( 'unittest log message 2', $this->object->getMessage() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRequest()
	{
		$this->assertEquals( 'UT request', $this->object->getRequest() );
	}


	public function testSetRequest()
	{
		$this->object->setRequest( 'UT request 2' );
		$this->assertEquals( 'UT request 2', $this->object->getRequest() );
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

		$list = $entries = array(
			'log.id' => 1,
			'log.siteid' => 2,
			'log.priority' => 1,
			'log.facility' => 'UT facility',
			'log.timestamp' => '2010-01-01 00:00:00',
			'log.message' => 'unittest log message',
			'log.request' => 'UT request',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( ['log.siteid' => 2, 'log.timestamp' => '2010-01-01 00:00:00'], $entries );
		$this->assertEquals( $list['log.id'], $item->getId() );
		$this->assertEquals( $list['log.priority'], $item->getPriority() );
		$this->assertEquals( $list['log.facility'], $item->getFacility() );
		$this->assertEquals( $list['log.message'], $item->getMessage() );
		$this->assertEquals( $list['log.request'], $item->getRequest() );
		$this->assertEquals( '', $item->getSiteId() );
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

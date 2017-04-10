<?php

namespace Aimeos\MAdmin\Cache\Item;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
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
			'id' => 'product/id/1:detail-body',
			'siteid' => 1,
			'value' => 'test',
			'expire' => '2000-01-01 00:00:00',
			'tags' => array( 'tag:1', 'tag:2' ),
		);

		$this->object = new \Aimeos\MAdmin\Cache\Item\Standard( $this->values );
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
		$this->assertEquals( 'product/id/1:detail-body', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetId()
	{
		$this->object->setId( 'product/id/2:detail-header' );
		$this->assertEquals( 'product/id/2:detail-header', $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
	}


	public function testGetValue()
	{
		$this->assertEquals( 'test', $this->object->getValue() );
	}


	public function testSetValue()
	{
		$this->object->setValue( 'test2' );
		$this->assertEquals( 'test2', $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeExpire()
	{
		$this->assertEquals( '2000-01-01 00:00:00', $this->object->getTimeExpire() );
	}


	public function testSetTimeExpire()
	{
		$this->object->setTimeExpire( '2100-01-01 00:00:00' );
		$this->assertEquals( '2100-01-01 00:00:00', $this->object->getTimeExpire() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTags()
	{
		$this->assertEquals( array( 'tag:1', 'tag:2' ), $this->object->getTags() );
	}


	public function testSetTags()
	{
		$this->object->setTags( array( 'tag:1', 'tag:3' ) );
		$this->assertEquals( array( 'tag:1', 'tag:3' ), $this->object->getTags() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'cache', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MAdmin\Cache\Item\Standard();

		$list = array(
			'cache.id' => 'product/id/1:detail-body',
			'cache.siteid' => 2,
			'cache.value' => 'test',
			'cache.expire' => '2000-01-01 00:00:00',
			'cache.tags' => array( 'tag1', 'tag2' ),
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['cache.id'], $item->getId() );
		$this->assertEquals( $list['cache.value'], $item->getValue() );
		$this->assertEquals( $list['cache.expire'], $item->getTimeExpire() );
		$this->assertEquals( $list['cache.tags'], $item->getTags() );
		$this->assertNull( $item->getSiteId() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( 5, count( $list ) );
		$this->assertEquals( 'product/id/1:detail-body', $list['cache.id'] );
		$this->assertEquals( 1, $list['cache.siteid'] );
		$this->assertEquals( 'test', $list['cache.value'] );
		$this->assertEquals( '2000-01-01 00:00:00', $list['cache.expire'] );
		$this->assertEquals( array( 'tag:1', 'tag:2' ), $list['cache.tags'] );
	}
}

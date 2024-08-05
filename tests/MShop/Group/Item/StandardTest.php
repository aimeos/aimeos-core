<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */

namespace Aimeos\MShop\Group\Item\Group;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'group.id' => '123',
			'group.siteid' => '456',
			'group.code' => 'unitgroup',
			'group.label' => 'unittest',
			'group.ctime' => '1970-01-01 00:00:00',
			'group.mtime' => '2000-01-01 00:00:00',
			'group.editor' => 'unittest',
		);

		$this->object = new \Aimeos\MShop\Group\Item\Standard( 'group.', $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->values );
	}


	public function testGetId()
	{
		$this->assertEquals( 123, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Group\Item\Iface::class, $return );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 456, $this->object->getSiteId() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unitgroup', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'unitgroup2' );

		$this->assertInstanceOf( \Aimeos\MShop\Group\Item\Iface::class, $return );
		$this->assertEquals( 'unitgroup2', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unittest1' );

		$this->assertInstanceOf( \Aimeos\MShop\Group\Item\Iface::class, $return );
		$this->assertEquals( 'unittest1', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'group', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Group\Item\Standard( 'group.', [] );

		$list = $entries = array(
			'group.id' => 12,
			'group.code' => 'unitgroup',
			'group.label' => 'unittest12',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['group.id'], $item->getId() );
		$this->assertEquals( $list['group.code'], $item->getCode() );
		$this->assertEquals( $list['group.label'], $item->getLabel() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['group.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['group.siteid'] );
		$this->assertEquals( $this->object->getCode(), $list['group.code'] );
		$this->assertEquals( $this->object->getLabel(), $list['group.label'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['group.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['group.mtime'] );
		$this->assertEquals( $this->object->editor(), $list['group.editor'] );
	}
}

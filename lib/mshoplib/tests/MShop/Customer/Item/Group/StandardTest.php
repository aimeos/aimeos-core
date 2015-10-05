<?php

namespace Aimeos\MShop\Customer\Item\Group;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'id' => '123',
			'siteid' => '456',
			'code' => 'unitgroup',
			'label' => 'unittest',
			'ctime' => '1970-01-01 00:00:00',
			'mtime' => '2000-01-01 00:00:00',
			'editor' => 'unittest',
		);

		$this->object = new \Aimeos\MShop\Customer\Item\Group\Standard( $this->values );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->values );
	}


	public function testGetId()
	{
		$this->assertEquals( 123, $this->object->getId() );
	}


	public function testSetId()
	{
		$this->object->setId( null );
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
		$this->object->setCode( 'unitgroup2' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unitgroup2', $this->object->getCode() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->object->setLabel( 'unittest2' );
		$this->assertTrue( $this->object->isModified() );
		$this->assertEquals( 'unittest2', $this->object->getLabel() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Customer\Item\Group\Standard();

		$list = array(
			'customer.group.id' => 12,
			'customer.group.code' => 'unitgroup',
			'customer.group.label' => 'unittest12',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );
		$this->assertEquals( $list['customer.group.id'], $item->getId() );
		$this->assertEquals( $list['customer.group.code'], $item->getCode() );
		$this->assertEquals( $list['customer.group.label'], $item->getLabel() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['customer.group.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['customer.group.siteid'] );
		$this->assertEquals( $this->object->getCode(), $list['customer.group.code'] );
		$this->assertEquals( $this->object->getLabel(), $list['customer.group.label'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['customer.group.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['customer.group.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $list['customer.group.editor'] );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Customer_Item_Group_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_values;


	protected function setUp()
	{
		$this->_values = array(
			'id' => '123',
			'siteid' => '456',
			'code' => 'unitgroup',
			'label' => 'unittest',
			'ctime' => '1970-01-01 00:00:00',
			'mtime' => '2000-01-01 00:00:00',
			'editor' => 'unittest',
		);

		$this->_object = new MShop_Customer_Item_Group_Default( $this->_values );
	}


	protected function tearDown()
	{
		unset( $this->_object, $this->_values );
	}


	public function testGetId()
	{
		$this->assertEquals( 123, $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertNull( $this->_object->getId() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 456, $this->_object->getSiteId() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unitgroup', $this->_object->getCode() );
	}


	public function testSetCode()
	{
		$this->_object->setCode( 'unitgroup2' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'unitgroup2', $this->_object->getCode() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest', $this->_object->getLabel() );
	}


	public function testSetLabel()
	{
		$this->_object->setLabel( 'unittest2' );
		$this->assertTrue( $this->_object->isModified() );
		$this->assertEquals( 'unittest2', $this->_object->getLabel() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testFromArray()
	{
		$item = new MShop_Customer_Item_Group_Default();

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
		$list = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( $this->_object->getId(), $list['customer.group.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['customer.group.siteid'] );
		$this->assertEquals( $this->_object->getCode(), $list['customer.group.code'] );
		$this->assertEquals( $this->_object->getLabel(), $list['customer.group.label'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['customer.group.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['customer.group.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['customer.group.editor'] );
	}
}

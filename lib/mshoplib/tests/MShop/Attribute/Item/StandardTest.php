<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MShop\Attribute\Item;


/**
 * Test class for \Aimeos\MShop\Attribute\Item\Example.
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
			'attribute.id' => 999,
			'attribute.domain' => 'text',
			'attribute.code' => 'X12345',
			'attribute.status' => 1,
			'attribute.typeid' => 3,
			'attribute.type' => 'unittest',
			'attribute.typename' => 'Unit test',
			'attribute.position' => 0,
			'attribute.label' => 'size',
			'attribute.siteid' => 99,
			'attribute.mtime' => '2011-01-01 00:00:02',
			'attribute.ctime' => '2011-01-01 00:00:01',
			'attribute.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Attribute\Item\Standard( $this->values );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 999, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( 999 );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 999, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'unittest', $this->object->getType() );
	}


	public function testGetTypeName()
	{
		$this->assertEquals( 'Unit test', $this->object->getTypeName() );
	}


	public function testGetTypeId()
	{
		$this->assertEquals( 3, $this->object->getTypeId() );
	}


	public function testSetTypeId()
	{
		$return = $this->object->setTypeId( 5 );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 5, $this->object->getTypeId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'text', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'TestDom' );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 'TestDom', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'X12345', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'flobee' );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 'flobee', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 0, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 1 );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 1, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'size', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'weight' );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 'weight', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 4 );

		$this->assertInstanceOf( '\Aimeos\MShop\Attribute\Item\Iface', $return );
		$this->assertEquals( 4, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'attribute', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Attribute\Item\Standard();

		$list = array(
			'attribute.id' => 1,
			'attribute.siteid' => 2,
			'attribute.code' => 'test',
			'attribute.domain' => 'product',
			'attribute.status' => '0',
			'attribute.typeid' => 3,
			'attribute.type' => 'testtype',
			'attribute.typename' => 'Testtype',
			'attribute.label' => 'test attribute',
			'attribute.position' => 10,
			'attribute.ctime' => '2000-01-01 00:00:00',
			'attribute.mtime' => '2001-01-01 00:00:00',
			'attribute.editor' => 'test',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['attribute.id'], $item->getId() );
		$this->assertEquals( $list['attribute.code'], $item->getCode() );
		$this->assertEquals( $list['attribute.domain'], $item->getDomain() );
		$this->assertEquals( $list['attribute.status'], $item->getStatus() );
		$this->assertEquals( $list['attribute.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['attribute.label'], $item->getLabel() );
		$this->assertEquals( $list['attribute.position'], $item->getPosition() );
		$this->assertNull( $item->getSiteId() );
		$this->assertNull( $item->getTypeName() );
		$this->assertNull( $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['attribute.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['attribute.code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['attribute.domain'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['attribute.status'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['attribute.typeid'] );
		$this->assertEquals( $this->object->getTypeName(), $arrayObject['attribute.typename'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['attribute.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['attribute.label'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['attribute.position'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['attribute.siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['attribute.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['attribute.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['attribute.editor'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}

}

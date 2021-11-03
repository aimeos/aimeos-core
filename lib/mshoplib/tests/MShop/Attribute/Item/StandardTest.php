<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Attribute\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'attribute.id' => 999,
			'attribute.domain' => 'text',
			'attribute.code' => 'X12345',
			'attribute.status' => 1,
			'attribute.type' => 'unittest',
			'attribute.position' => 0,
			'attribute.label' => 'size',
			'attribute.siteid' => 99,
			'attribute.mtime' => '2011-01-01 00:00:02',
			'attribute.ctime' => '2011-01-01 00:00:01',
			'attribute.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Attribute\Item\Standard( $this->values );
	}


	protected function tearDown() : void
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

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
		$this->assertEquals( 999, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetKey()
	{
		$this->assertEquals( '012f8b3a33e1e75e84b5de3fcba4f42f', $this->object->getKey() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'unittest', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'default' );

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
		$this->assertEquals( 'default', $this->object->getType() );
		$this->assertEquals( true, $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'text', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'TestDom' );

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Attribute\Item\Iface::class, $return );
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

		$list = $entries = array(
			'attribute.id' => 1,
			'attribute.code' => 'test',
			'attribute.domain' => 'product',
			'attribute.status' => '0',
			'attribute.type' => 'testtype',
			'attribute.label' => 'test attribute',
			'attribute.position' => 10,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['attribute.id'], $item->getId() );
		$this->assertEquals( $list['attribute.code'], $item->getCode() );
		$this->assertEquals( $list['attribute.domain'], $item->getDomain() );
		$this->assertEquals( $list['attribute.status'], $item->getStatus() );
		$this->assertEquals( $list['attribute.type'], $item->getType() );
		$this->assertEquals( $list['attribute.label'], $item->getLabel() );
		$this->assertEquals( $list['attribute.position'], $item->getPosition() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) + 1, count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['attribute.id'] );
		$this->assertEquals( $this->object->getKey(), $arrayObject['attribute.key'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['attribute.code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['attribute.domain'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['attribute.status'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['attribute.type'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['attribute.label'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['attribute.position'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['attribute.siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['attribute.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['attribute.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['attribute.editor'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setAvailable( false );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsAvailableOnStatus()
	{
		$this->assertTrue( $this->object->isAvailable() );
		$this->object->setStatus( 0 );
		$this->assertFalse( $this->object->isAvailable() );
		$this->object->setStatus( -1 );
		$this->assertFalse( $this->object->isAvailable() );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

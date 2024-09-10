<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Supplier\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = [
			'supplier.id' => 541,
			'supplier.siteid' => 99,
			'supplier.label' => 'unitObject',
			'supplier.code' => 'unitCode',
			'supplier.position' => 1,
			'supplier.status' => 4,
			'supplier.mtime' => '2011-01-01 00:00:02',
			'supplier.ctime' => '2011-01-01 00:00:01',
			'supplier.editor' => 'unitTestUser'
		];

		$addresses = [
			new \Aimeos\MShop\Supplier\Item\Address\Standard( 'supplier.address.', ['supplier.address.position' => 0] ),
			new \Aimeos\MShop\Supplier\Item\Address\Standard( 'supplier.address.', ['supplier.address.position' => 1] ),
		];

		$this->object = new \Aimeos\MShop\Supplier\Item\Standard( 'supplier.', $this->values + ['.addritems' => $addresses] );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 541, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unitObject', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'newName' );

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $return );
		$this->assertEquals( 'newName', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'unitCode', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'newCode' );

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $return );
		$this->assertEquals( 'newCode', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 4, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
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


	public function testGetAddressItems()
	{
		$i = 0;
		$list = $this->object->getAddressItems();
		$this->assertEquals( 2, count( $list ) );

		foreach( $list as $item )
		{
			$this->assertEquals( $i++, $item->getPosition() );
			$this->assertInstanceOf( \Aimeos\MShop\Supplier\Item\Address\Iface::class, $item );
		}
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'supplier', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Supplier\Item\Standard( 'supplier.' );

		$list = $entries = array(
			'supplier.id' => 1,
			'supplier.code' => 'test',
			'supplier.label' => 'test item',
			'supplier.position' => 1,
			'supplier.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['supplier.id'], $item->getId() );
		$this->assertEquals( $list['supplier.code'], $item->getCode() );
		$this->assertEquals( $list['supplier.label'], $item->getLabel() );
		$this->assertEquals( $list['supplier.status'], $item->getStatus() );
		$this->assertEquals( $list['supplier.position'], $item->getPosition() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['supplier.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['supplier.siteid'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['supplier.label'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['supplier.code'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['supplier.status'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['supplier.position'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['supplier.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['supplier.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['supplier.editor'] );
	}
}

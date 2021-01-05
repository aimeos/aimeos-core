<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Common\Item\Type;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'common.type.id'   => 1,
			'common.type.siteid' => 2,
			'common.type.code' => 'code',
			'common.type.domain' => 'domain',
			'common.type.label' => 'label',
			'common.type.name' => 'name',
			'common.type.position' => 5,
			'common.type.status' => 1,
			'common.type.mtime' => '2011-01-01 00:00:02',
			'common.type.ctime' => '2011-01-01 00:00:01',
			'common.type.editor' => 'unitTestUser',
		);

		$this->object = new \Aimeos\MShop\Common\Item\Type\Standard( 'common.type.', $this->values );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertTrue( $this->object->isModified() );
		$this->assertNull( $this->object->getId() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'code', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$return = $this->object->setCode( 'code2' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertEquals( 'code2', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'domain', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'domain2' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertEquals( 'domain2', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetName()
	{
		$this->assertEquals( 'name', $this->object->getName() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'label', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'label2' );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertEquals( 'label2', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPosition()
	{
		$this->assertEquals( 5, $this->object->getPosition() );
	}

	public function testSetPosition()
	{
		$return = $this->object->setPosition( 4 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertEquals( 4, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}

	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Type\Iface::class, $return );
		$this->assertEquals( 0, $this->object->getStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 2, $this->object->getSiteId() );
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
		$this->assertEquals( 'common/type', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Common\Item\Type\Standard( 'common.type.' );

		$list = $entries = array(
			'common.type.id' => 8,
			'common.type.code' => 'test',
			'common.type.domain' => 'testDomain',
			'common.type.label' => 'test item',
			'common.type.position' => 2,
			'common.type.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );

		$this->assertEquals( $list['common.type.id'], $item->getId() );
		$this->assertEquals( $list['common.type.code'], $item->getCode() );
		$this->assertEquals( $list['common.type.domain'], $item->getDomain() );
		$this->assertEquals( $list['common.type.label'], $item->getName() ); // fallback to label
		$this->assertEquals( $list['common.type.label'], $item->getLabel() );
		$this->assertEquals( $list['common.type.position'], $item->getPosition() );
		$this->assertEquals( $list['common.type.status'], $item->getStatus() );
		$this->assertEquals( '', $item->getSiteId() );
	}

	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['common.type.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['common.type.code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['common.type.domain'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['common.type.name'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['common.type.label'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['common.type.position'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['common.type.status'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['common.type.siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['common.type.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['common.type.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['common.type.editor'] );
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

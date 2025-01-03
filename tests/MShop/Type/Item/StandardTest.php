<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 */


namespace Aimeos\MShop\Type\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'type.id'   => 1,
			'type.siteid' => 2,
			'type.code' => 'code',
			'type.for' => 'text',
			'type.domain' => 'domain',
			'type.label' => 'label',
			'type.i18n' => ['de' => 'name'],
			'type.position' => 5,
			'type.status' => 1,
			'type.mtime' => '2011-01-01 00:00:02',
			'type.ctime' => '2011-01-01 00:00:01',
			'type.editor' => 'unitTestUser',
			'.language' => 'de'
		);

		$this->object = new \Aimeos\MShop\Type\Item\Standard( 'type.', $this->values );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
		$this->assertEquals( 'domain2', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetFor()
	{
		$this->assertEquals( 'text', $this->object->getFor() );
	}


	public function testSetFor()
	{
		$return = $this->object->setFor( 'media' );

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
		$this->assertEquals( 'media', $this->object->getFor() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetI18n()
	{
		$this->assertEquals( ['de' => 'name'], $this->object->getI18n() );
	}


	public function testSetI18n()
	{
		$return = $this->object->setI18n( ['de' => 'label2'] );

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
		$this->assertEquals( ['de' => 'label2'], $this->object->getI18n() );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Type\Item\Iface::class, $return );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'type', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Type\Item\Standard( 'type.', ['.language' => 'de'] );

		$list = $entries = array(
			'type.id' => 8,
			'type.code' => 'test',
			'type.domain' => 'testDomain',
			'type.for' => 'product',
			'type.i18n' => ['de' => 'test eintrag'],
			'type.label' => 'test item',
			'type.position' => 2,
			'type.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );

		$this->assertEquals( $list['type.id'], $item->getId() );
		$this->assertEquals( $list['type.code'], $item->getCode() );
		$this->assertEquals( $list['type.domain'], $item->getDomain() );
		$this->assertEquals( $list['type.for'], $item->getFor() );
		$this->assertEquals( $list['type.i18n'], $item->getI18n() );
		$this->assertEquals( $list['type.label'], $item->getLabel() );
		$this->assertEquals( $list['type.position'], $item->getPosition() );
		$this->assertEquals( $list['type.status'], $item->getStatus() );
		$this->assertEquals( 'test eintrag', $item->getName() );
		$this->assertEquals( '', $item->getSiteId() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['type.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['type.code'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['type.domain'] );
		$this->assertEquals( $this->object->getFor(), $arrayObject['type.for'] );
		$this->assertEquals( $this->object->getI18n(), $arrayObject['type.i18n'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['type.name'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['type.label'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['type.position'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['type.status'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['type.siteid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['type.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['type.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['type.editor'] );
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

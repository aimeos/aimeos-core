<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */

namespace Aimeos\MShop\Locale\Item\Currency;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'locale.currency.id' => 'EUR',
			'locale.currency.label' => 'Euro',
			'locale.currency.siteid' => 1,
			'locale.currency.status' => 1,
			'locale.currency.mtime' => '2011-01-01 00:00:02',
			'locale.currency.ctime' => '2011-01-01 00:00:01',
			'locale.currency.editor' => 'unitTestUser'
		);
		$this->object = new \Aimeos\MShop\Locale\Item\Currency\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( 'EUR', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Currency\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 'XXX' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Currency\Iface::class, $return );
		$this->assertEquals( 'XXX', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetIdLength()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setId( 'EU' );
	}


	public function testSetIdNumeric()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setId( 123 );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'EUR', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'USD' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Currency\Iface::class, $return );
		$this->assertEquals( 'USD', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetCodeInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setCode( 'XXXX' );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'Euro', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'OtherName' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Currency\Iface::class, $return );
		$this->assertEquals( 'OtherName', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 1, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Currency\Iface::class, $return );
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
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'locale/currency', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Locale\Item\Currency\Standard();

		$list = $entries = array(
			'locale.currency.id' => 'EUR',
			'locale.currency.code' => 'EUR',
			'locale.currency.label' => 'test item',
			'locale.currency.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['locale.currency.id'], $item->getId() );
		$this->assertEquals( $list['locale.currency.code'], $item->getCode() );
		$this->assertEquals( $list['locale.currency.label'], $item->getLabel() );
		$this->assertEquals( $list['locale.currency.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( ( count( $this->values ) + 1 ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.currency.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['locale.currency.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['locale.currency.label'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.currency.siteid'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.currency.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.currency.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.currency.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.currency.editor'] );
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

}

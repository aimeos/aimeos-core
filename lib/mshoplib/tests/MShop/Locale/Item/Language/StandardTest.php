<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MShop\Locale\Item\Language;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'locale.language.id' => 'es',
			'locale.language.label' => 'spanish',
			'locale.language.siteid' => 1,
			'locale.language.status' => 9,
			'locale.language.mtime' => '2011-01-01 00:00:02',
			'locale.language.ctime' => '2011-01-01 00:00:01',
			'locale.language.editor' => 'unitTestUser'
		);
		$this->object = new \Aimeos\MShop\Locale\Item\Language\Standard( $this->values );
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
		$this->assertEquals( 'es', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Language\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 'de' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Language\Iface::class, $return );
		$this->assertEquals( 'de', $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetIdLength()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setId( 'espania' );
	}


	public function testSetIdNumeric()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setId( 123 );
	}


	public function testGetCode()
	{
		$this->assertEquals( $this->object->getId(), $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'DE' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Language\Iface::class, $return );
		$this->assertEquals( 'de', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetCodeInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setCode( 'XXXX' );
	}


	public function testSetCodeCountry()
	{
		$this->object->setCode( 'de_DE' );
		$this->assertEquals( 'de_DE', $this->object->getCode() );

		$this->object->setCode( 'DE_DE' );
		$this->assertEquals( 'de_DE', $this->object->getCode() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'spanish', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'OtherName' );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Language\Iface::class, $return );
		$this->assertEquals( 'OtherName', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 1, $this->object->getSiteId() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 9, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Locale\Item\Language\Iface::class, $return );
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
		$this->assertEquals( 'locale/language', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Locale\Item\Language\Standard();

		$list = $entries = array(
				'locale.language.id' => 'de',
				'locale.language.code' => 'de',
				'locale.language.label' => 'test item',
				'locale.language.status' => 1,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['locale.language.id'], $item->getId() );
		$this->assertEquals( $list['locale.language.code'], $item->getCode() );
		$this->assertEquals( $list['locale.language.label'], $item->getLabel() );
		$this->assertEquals( $list['locale.language.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( ( count( $this->values ) + 1 ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['locale.language.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['locale.language.code'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['locale.language.label'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['locale.language.siteid'] );
		$this->assertEquals( $this->object->getStatus(), $arrayObject['locale.language.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['locale.language.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['locale.language.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['locale.language.editor'] );
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

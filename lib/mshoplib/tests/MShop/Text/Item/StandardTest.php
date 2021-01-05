<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Text\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'text.id' => 10,
			'text.siteid' => 99,
			'text.languageid' => 'de',
			'text.type' => 'name',
			'text.label' => 'unittest label',
			'text.domain' => 'product',
			'text.content' => 'unittest text',
			'text.status' => 2,
			'text.mtime' => '2011-01-01 00:00:02',
			'text.ctime' => '2011-01-01 00:00:01',
			'text.editor' => 'unitTestUser',
			'.languageid' => 'de',
		);

		$this->object = new \Aimeos\MShop\Text\Item\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetId()
	{
		$this->assertEquals( '10', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'name', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
		$this->assertEquals( 'test', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetLabel()
	{
		$this->assertEquals( 'unittest label', $this->object->getLabel() );
	}


	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'unittest set label' );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
		$this->assertEquals( 'unittest set label', $this->object->getLabel() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}


	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'catalog' );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
		$this->assertEquals( 'catalog', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetContent()
	{
		$this->assertEquals( 'unittest text', $this->object->getContent() );
	}


	public function testSetContent()
	{
		$return = $this->object->setContent( 'unit test text' );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
		$this->assertEquals( 'unit test text', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#x0630;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( 'ذرز', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x27144;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->object->getContent() );

		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetContentUtf8Invalid()
	{
		$this->object->setContent( chr( 0x96 ) . 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ' );
		$this->assertEquals( 'укгезәөшөхзәхөшк2049һһлдябчсячмииюсит.июбҗрарэ', $this->object->getContent() );

		$this->object->setContent( mb_convert_encoding( '&#xD800;&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ) );
		$this->assertEquals( mb_convert_encoding( '&#x0631;&#x0632;', 'UTF-8', 'HTML-ENTITIES' ), $this->object->getContent() );
	}


	public function testGetStatus()
	{
		$this->assertEquals( 2, $this->object->getStatus() );
	}


	public function testSetStatus()
	{
		$return = $this->object->setStatus( 0 );

		$this->assertInstanceOf( \Aimeos\MShop\Text\Item\Iface::class, $return );
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
		$this->assertEquals( 'text', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Text\Item\Standard();

		$list = $entries = array(
			'text.id' => 1,
			'text.type' => 'test',
			'text.languageid' => 'de',
			'text.label' => 'test item',
			'text.domain' => 'product',
			'text.content' => 'test content',
			'text.status' => 0,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['text.id'], $item->getId() );
		$this->assertEquals( $list['text.type'], $item->getType() );
		$this->assertEquals( $list['text.languageid'], $item->getLanguageId() );
		$this->assertEquals( $list['text.label'], $item->getLabel() );
		$this->assertEquals( $list['text.domain'], $item->getDomain() );
		$this->assertEquals( $list['text.content'], $item->getContent() );
		$this->assertEquals( $list['text.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$data = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) - 1, count( $data ) );

		$this->assertEquals( $this->object->getId(), $data['text.id'] );
		$this->assertEquals( $this->object->getSiteId(), $data['text.siteid'] );
		$this->assertEquals( $this->object->getLanguageId(), $data['text.languageid'] );
		$this->assertEquals( $this->object->getType(), $data['text.type'] );
		$this->assertEquals( $this->object->getLabel(), $data['text.label'] );
		$this->assertEquals( $this->object->getDomain(), $data['text.domain'] );
		$this->assertEquals( $this->object->getContent(), $data['text.content'] );
		$this->assertEquals( $this->object->getStatus(), $data['text.status'] );
		$this->assertEquals( $this->object->getTimeCreated(), $data['text.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $data['text.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $data['text.editor'] );
	}


	public function testGetLanguageId()
	{
		$this->assertEquals( 'de', $this->object->getLanguageId() );
	}


	public function testSetLanguageId()
	{
		$this->object->setLanguageId( 'en' );
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}


	public function testSetLanguageIdInvalid()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setLanguageId( '10' );
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

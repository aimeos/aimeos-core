<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Tag\Item;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'tag.id' => 987,
			'tag.siteid' => 99,
			'tag.typeid' => 44,
			'tag.domain' => 'product',
			'tag.languageid' => 'en',
			'tag.type' => 'taste',
			'tag.typename' => 'Taste',
			'tag.label' => 'salty',
			'tag.mtime' => '2011-01-01 00:00:02',
			'tag.ctime' => '2011-01-01 00:00:01',
			'tag.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Tag\Item\Standard( $this->values );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetId()
	{
		$this->assertEquals( 987, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( '\Aimeos\MShop\Tag\Item\Iface', $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetDomain()
	{
		$this->assertEquals( 'product', $this->object->getDomain() );
	}

	public function testSetDomain()
	{
		$return = $this->object->setDomain( 'catalog' );

		$this->assertInstanceOf( '\Aimeos\MShop\Tag\Item\Iface', $return );
		$this->assertEquals( 'catalog', $this->object->getDomain() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId( 'fr' );

		$this->assertInstanceOf( '\Aimeos\MShop\Tag\Item\Iface', $return );
		$this->assertEquals( 'fr', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$return = $this->object->setTypeId( 33 );

		$this->assertInstanceOf( '\Aimeos\MShop\Tag\Item\Iface', $return );
		$this->assertEquals( 33, $this->object->getTypeId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'taste', $this->object->getType() );
	}

	public function testGetTypeName()
	{
		$this->assertEquals( 'Taste', $this->object->getTypeName() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( 'salty', $this->object->getLabel() );
	}

	public function testSetLabel()
	{
		$return = $this->object->setLabel( 'bitter' );

		$this->assertInstanceOf( '\Aimeos\MShop\Tag\Item\Iface', $return );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( 'bitter', $this->object->getLabel() );
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
		$this->assertEquals( 'tag', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Tag\Item\Standard();

		$list = array(
			'tag.id' => 1,
			'tag.typeid' => 2,
			'tag.type' => 'test',
			'tag.typename' => 'Test',
			'tag.domain' => 'product',
			'tag.label' => 'test item',
			'tag.languageid' => 'de',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( [], $unknown );

		$this->assertEquals( $list['tag.id'], $item->getId() );
		$this->assertEquals( $list['tag.typeid'], $item->getTypeId() );
		$this->assertEquals( $list['tag.domain'], $item->getDomain() );
		$this->assertEquals( $list['tag.label'], $item->getLabel() );
		$this->assertEquals( $list['tag.languageid'], $item->getLanguageId() );
		$this->assertNull( $item->getSiteId() );
		$this->assertNull( $item->getTypeName() );
		$this->assertNull( $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['tag.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['tag.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['tag.type'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['tag.typeid'] );
		$this->assertEquals( $this->object->getTypeName(), $arrayObject['tag.typename'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['tag.languageid'] );
		$this->assertEquals( $this->object->getLabel(), $arrayObject['tag.label'] );
		$this->assertEquals( $this->object->getDomain(), $arrayObject['tag.domain'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['tag.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['tag.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['tag.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

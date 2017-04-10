<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2016
 */


namespace Aimeos\MShop\Product\Item\Property;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	protected function setUp()
	{
		$this->values = array(
			'product.property.id' => 987,
			'product.property.parentid' => 11,
			'product.property.siteid' => 99,
			'product.property.typeid' => 44,
			'product.property.languageid' => 'en',
			'product.property.type' => 'width',
			'product.property.typename' => 'Width',
			'product.property.value' => '30.0',
			'product.property.mtime' => '2011-01-01 00:00:02',
			'product.property.ctime' => '2011-01-01 00:00:01',
			'product.property.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Product\Item\Property\Standard( $this->values );
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

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Property\Iface', $return );
		$this->assertNull( $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetLanguageId()
	{
		$this->assertEquals( 'en', $this->object->getLanguageId() );
	}

	public function testSetLanguageId()
	{
		$return = $this->object->setLanguageId('fr');

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Property\Iface', $return );
		$this->assertEquals( 'fr', $this->object->getLanguageId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 11, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$return = $this->object->setParentId( 22 );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Property\Iface', $return );
		$this->assertEquals( 22, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTypeId()
	{
		$this->assertEquals( 44, $this->object->getTypeId() );
	}

	public function testSetTypeId()
	{
		$return = $this->object->setTypeId(33);

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Property\Iface', $return );
		$this->assertEquals( 33, $this->object->getTypeId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetType()
	{
		$this->assertEquals( 'width', $this->object->getType() );
	}

	public function testGetTypeName()
	{
		$this->assertEquals( 'Width', $this->object->getTypeName() );
	}

	public function testGetValue()
	{
		$this->assertEquals( '30.0', $this->object->getValue() );
	}

	public function testSetValue()
	{
		$return = $this->object->setValue( '15.00' );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Property\Iface', $return );
		$this->assertEquals( '15.00', $this->object->getValue() );
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
		$this->assertEquals( 'product/property', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Product\Item\Property\Standard();

		$list = array(
			'product.property.parentid' => 1,
			'product.property.typeid' => 2,
			'product.property.type' => 'test',
			'product.property.typename' => 'Test',
			'product.property.languageid' => 'de',
			'product.property.value' => 'value',
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals([], $unknown);

		$this->assertEquals($list['product.property.parentid'], $item->getParentId());
		$this->assertEquals($list['product.property.typeid'], $item->getTypeId());
		$this->assertEquals($list['product.property.languageid'], $item->getLanguageId());
		$this->assertEquals($list['product.property.value'], $item->getValue());
		$this->assertNull( $item->getSiteId() );
		$this->assertNull( $item->getTypeName() );
		$this->assertNull( $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['product.property.id'] );
		$this->assertEquals( $this->object->getSiteId(), $arrayObject['product.property.siteid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.property.type'] );
		$this->assertEquals( $this->object->getTypeId(), $arrayObject['product.property.typeid'] );
		$this->assertEquals( $this->object->getTypeName(), $arrayObject['product.property.typename'] );
		$this->assertEquals( $this->object->getLanguageId(), $arrayObject['product.property.languageid'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['product.property.type'] );
		$this->assertEquals( $this->object->getValue(), $arrayObject['product.property.value'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['product.property.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['product.property.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['product.property.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

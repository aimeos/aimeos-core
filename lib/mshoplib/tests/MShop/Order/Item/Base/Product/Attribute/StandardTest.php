<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base\Product\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.base.product.attribute.id' => 4,
			'order.base.product.attribute.siteid' => 99,
			'order.base.product.attribute.attributeid' => 22,
			'order.base.product.attribute.parentid' => 11,
			'order.base.product.attribute.type' => 'UnitType',
			'order.base.product.attribute.code' => 'size',
			'order.base.product.attribute.value' => '30',
			'order.base.product.attribute.name' => 'small',
			'order.base.product.attribute.quantity' => 2,
			'order.base.product.attribute.mtime' => '2011-01-06 13:20:34',
			'order.base.product.attribute.ctime' => '2011-01-01 00:00:01',
			'order.base.product.attribute.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Base\Product\Attribute\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 4, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 8 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 8, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testSetSiteId()
	{
		$this->object->setSiteId( 100 );
		$this->assertEquals( 100, $this->object->getSiteId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAttributeId()
	{
		$this->assertEquals( 22, $this->object->getAttributeId() );
	}


	public function testSetAttributeId()
	{
		$return = $this->object->setAttributeId( 44 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 44, $this->object->getAttributeId() );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '', $this->object->setAttributeId( null )->getAttributeId() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 11, $this->object->getParentId() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 33 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 33, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'UnitType', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'testType' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 'testType', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'size', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'weight' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 'weight', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( '30', $this->object->getValue() );
	}


	public function testSetValue()
	{
		$return = $this->object->setValue( 36 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 36, $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'small', $this->object->getName() );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'medium' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 'medium', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '', $this->object->setName( null )->getName() );
	}


	public function testGetQuantity()
	{
		$this->assertEquals( 2, $this->object->getQuantity() );
	}


	public function testSetQuantity()
	{
		$return = $this->object->setQuantity( 3 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( 3, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$regexp = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
		$this->assertRegExp( $regexp, $this->object->getTimeModified() );
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
		$this->assertEquals( 'order/base/product/attribute', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::create( \TestHelperMShop::getContext() );
		$item = $attrManager->search( $attrManager->filter() )->first();

		$return = $this->object->copyFrom( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $return );
		$this->assertEquals( $item->getId(), $this->object->getAttributeId() );
		$this->assertEquals( $item->getName(), $this->object->getName() );
		$this->assertEquals( $item->getType(), $this->object->getCode() );
		$this->assertEquals( $item->getCode(), $this->object->getValue() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Product\Attribute\Standard();

		$list = $entries = array(
			'order.base.product.attribute.id' => 1,
			'order.base.product.attribute.siteid' => 123,
			'order.base.product.attribute.attributeid' => 2,
			'order.base.product.attribute.parentid' => 3,
			'order.base.product.attribute.type' => 'variant',
			'order.base.product.attribute.code' => 'test',
			'order.base.product.attribute.value' => 'value',
			'order.base.product.attribute.name' => 'test item',
			'order.base.product.attribute.quantity' => 4,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['order.base.product.attribute.id'], $item->getId() );
		$this->assertEquals( $list['order.base.product.attribute.siteid'], $item->getSiteId() );
		$this->assertEquals( $list['order.base.product.attribute.attributeid'], $item->getAttributeId() );
		$this->assertEquals( $list['order.base.product.attribute.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.base.product.attribute.type'], $item->getType() );
		$this->assertEquals( $list['order.base.product.attribute.code'], $item->getCode() );
		$this->assertEquals( $list['order.base.product.attribute.value'], $item->getValue() );
		$this->assertEquals( $list['order.base.product.attribute.name'], $item->getName() );
		$this->assertEquals( $list['order.base.product.attribute.quantity'], $item->getQuantity() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.base.product.attribute.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.base.product.attribute.siteid'] );
		$this->assertEquals( $this->object->getAttributeId(), $list['order.base.product.attribute.attributeid'] );
		$this->assertEquals( $this->object->getParentId(), $list['order.base.product.attribute.parentid'] );
		$this->assertEquals( $this->object->getType(), $list['order.base.product.attribute.type'] );
		$this->assertEquals( $this->object->getCode(), $list['order.base.product.attribute.code'] );
		$this->assertEquals( $this->object->getValue(), $list['order.base.product.attribute.value'] );
		$this->assertEquals( $this->object->getName(), $list['order.base.product.attribute.name'] );
		$this->assertEquals( $this->object->getQuantity(), $list['order.base.product.attribute.quantity'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.product.attribute.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.base.product.attribute.ctime'] );
		$this->assertEquals( $this->object->getEditor(), $list['order.base.product.attribute.editor'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

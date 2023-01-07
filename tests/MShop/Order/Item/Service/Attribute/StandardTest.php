<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Item\Service\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.service.attribute.id' => 3,
			'order.service.attribute.siteid' => 99,
			'order.service.attribute.attributeid' => 22,
			'order.service.attribute.parentid' => 42,
			'order.service.attribute.type' => 'UnitType',
			'order.service.attribute.name' => 'UnitName',
			'order.service.attribute.code' => 'UnitCode',
			'order.service.attribute.value' => 'UnitValue',
			'order.service.attribute.price' => '1.00',
			'order.service.attribute.quantity' => 1,
			'order.service.attribute.mtime' => '2020-12-31 23:59:59',
			'order.service.attribute.ctime' => '2011-01-01 00:00:01',
			'order.service.attribute.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Service\Attribute\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 3, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 99 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 99, $this->object->getId() );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 44, $this->object->getAttributeId() );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '', $this->object->setAttributeId( null )->getAttributeId() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 42, $this->object->getParentId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 98 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 98, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'UnitType', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'testType' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 'testType', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'UnitCode', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'testCode' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 'testCode', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( 'UnitValue', $this->object->getValue() );
	}


	public function testSetValue()
	{
		$return = $this->object->setValue( 'custom' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 'custom', $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'UnitName', $this->object->getName() );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'testName' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 'testName', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );

		$this->assertEquals( '', $this->object->setName( null )->getName() );
	}


	public function testGetQuantity()
	{
		$this->assertEquals( 1, $this->object->getQuantity() );
	}


	public function testSetQuantity()
	{
		$return = $this->object->setQuantity( 3 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( 3, $this->object->getQuantity() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPrice()
	{
		$this->assertEquals( '1.00', $this->object->getPrice() );
	}


	public function testSetPrice()
	{
		$return = $this->object->setPrice( '3.75' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( '3.75', $this->object->getPrice() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPriceNull()
	{
		$return = $this->object->setPrice( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( null, $this->object->getPrice() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2020-12-31 23:59:59', $this->object->getTimeModified() );
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
		$this->assertEquals( 'order/service/attribute', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$attrManager = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' );
		$item = $attrManager->search( $attrManager->filter() )->first();

		$return = $this->object->copyFrom( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Attribute\Iface::class, $return );
		$this->assertEquals( $item->getId(), $this->object->getAttributeId() );
		$this->assertEquals( $item->getName(), $this->object->getName() );
		$this->assertEquals( $item->getType(), $this->object->getCode() );
		$this->assertEquals( $item->getCode(), $this->object->getValue() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Service\Attribute\Standard();

		$list = $entries = array(
			'order.service.attribute.id' => 1,
			'order.service.attribute.attributeid' => 2,
			'order.service.attribute.parentid' => 3,
			'order.service.attribute.type' => 'delivery',
			'order.service.attribute.code' => 'test',
			'order.service.attribute.value' => 'value',
			'order.service.attribute.name' => 'test item',
			'order.service.attribute.price' => '2.00',
			'order.service.attribute.quantity' => 4,
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.service.attribute.id'], $item->getId() );
		$this->assertEquals( $list['order.service.attribute.attributeid'], $item->getAttributeId() );
		$this->assertEquals( $list['order.service.attribute.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.service.attribute.type'], $item->getType() );
		$this->assertEquals( $list['order.service.attribute.code'], $item->getCode() );
		$this->assertEquals( $list['order.service.attribute.name'], $item->getName() );
		$this->assertEquals( $list['order.service.attribute.value'], $item->getValue() );
		$this->assertEquals( $list['order.service.attribute.price'], $item->getPrice() );
		$this->assertEquals( $list['order.service.attribute.quantity'], $item->getQuantity() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.service.attribute.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.service.attribute.siteid'] );
		$this->assertEquals( $this->object->getAttributeId(), $list['order.service.attribute.attributeid'] );
		$this->assertEquals( $this->object->getParentId(), $list['order.service.attribute.parentid'] );
		$this->assertEquals( $this->object->getType(), $list['order.service.attribute.type'] );
		$this->assertEquals( $this->object->getCode(), $list['order.service.attribute.code'] );
		$this->assertEquals( $this->object->getName(), $list['order.service.attribute.name'] );
		$this->assertEquals( $this->object->getValue(), $list['order.service.attribute.value'] );
		$this->assertEquals( $this->object->getPrice(), $list['order.service.attribute.price'] );
		$this->assertEquals( $this->object->getQuantity(), $list['order.service.attribute.quantity'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.service.attribute.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.service.attribute.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.service.attribute.mtime'] );
		$this->assertEquals( $this->object->editor(), $list['order.service.attribute.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

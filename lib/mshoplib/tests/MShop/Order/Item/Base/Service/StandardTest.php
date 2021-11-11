<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base\Service;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;
	private $price;
	private $attribute = [];


	protected function setUp() : void
	{
		$this->price = \Aimeos\MShop\Price\Manager\Factory::create( \TestHelperMShop::getContext() )->create();

		$attrValues = array(
			'order.base.service.attribute.id' => 3,
			'order.base.service.attribute.siteid' => 99,
			'order.base.service.attribute.parentid' => 42,
			'order.base.service.attribute.name' => 'UnitName',
			'order.base.service.attribute.type' => 'default',
			'order.base.service.attribute.code' => 'UnitCode',
			'order.base.service.attribute.value' => 'UnitValue',
			'order.base.service.attribute.mtime' => '2020-12-31 23:59:59',
			'order.base.service.attribute.ctime' => '2011-01-01 00:00:01',
			'order.base.service.attribute.editor' => 'unitTestUser'
		);

		$this->attribute = array( new \Aimeos\MShop\Order\Item\Base\Service\Attribute\Standard( $attrValues ) );

		$this->values = array(
			'order.base.service.id' => 1,
			'order.base.service.siteid' => 99,
			'order.base.service.serviceid' => 'ServiceID',
			'order.base.service.baseid' => 42,
			'order.base.service.code' => 'UnitCode',
			'order.base.service.name' => 'UnitName',
			'order.base.service.mediaurl' => 'Url for test',
			'order.base.service.position' => 1,
			'order.base.service.type' => 'payment',
			'order.base.service.mtime' => '2012-01-01 00:00:01',
			'order.base.service.ctime' => '2011-01-01 00:00:01',
			'order.base.service.editor' => 'unitTestUser'
		);

		$servItem = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'service' )->create();
		$this->object = new \Aimeos\MShop\Order\Item\Base\Service\Standard(
			$this->price, $this->values, $this->attribute, $servItem
		);
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetServiceItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $this->object->getServiceItem() );
		$this->assertNull( ( new \Aimeos\MShop\Order\Item\Base\Service\Standard( $this->price ) )->getServiceItem() );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
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


	public function testGetBaseId()
	{
		$this->assertEquals( 42, $this->object->getBaseId() );
	}


	public function testSetBaseId()
	{
		$return = $this->object->setBaseId( 111 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 111, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetBaseIdReset()
	{
		$return = $this->object->setBaseId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetServiceId()
	{
		$this->assertEquals( 'ServiceID', $this->object->getServiceId() );
	}


	public function testSetServiceId()
	{
		$return = $this->object->setServiceId( 'testServiceID' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'testServiceID', $this->object->getServiceId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( 'UnitCode', $this->object->getCode() );
	}


	public function testSetCode()
	{
		$return = $this->object->setCode( 'testCode' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'testCode', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'UnitName', $this->object->getName() );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'testName' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'testName', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetMediaUrl()
	{
		$this->assertEquals( 'Url for test', $this->object->getMediaUrl() );
	}


	public function testSetMediaUrl()
	{
		$return = $this->object->setMediaUrl( 'testUrl' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'testUrl', $this->object->getMediaUrl() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'payment', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'delivery' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'delivery', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPrice()
	{
		$this->assertSame( $this->price, $this->object->getPrice() );
	}


	public function testSetPrice()
	{
		$this->price->setCosts( '5.00' );
		$return = $this->object->setPrice( $this->price );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertFalse( $this->object->isModified() );
		$this->assertSame( $this->price, $this->object->getPrice() );
	}


	public function testGetPosition()
	{
		$this->assertEquals( 1, $this->object->getPosition() );
	}


	public function testSetPosition()
	{
		$return = $this->object->setPosition( 2 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionReset()
	{
		$return = $this->object->setPosition( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionInvalid()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setPosition( -1 );
	}


	public function testGetAttribute()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( 'value_001', $result );

		$result = $this->object->getAttribute( 'code_002', ['test_002'] );
		$this->assertEquals( 'value_002', $result );

		$result = $this->object->getAttribute( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result );

		$result = $this->object->getAttribute( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttribute( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributeItems( [] );

		$result = $this->object->getAttribute( 'code_001' );
		$this->assertEquals( null, $result );
	}


	public function testGetAttributeList()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setType( 'test_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_001' );
		$attrItem002->setType( 'test_001' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttribute( 'code_001', 'test_001' );
		$this->assertEquals( ['value_001', 'value_002'], $result );
	}


	public function testGetAttributeItem()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_002' );
		$attrItem002->setType( 'test_002' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttributeItem( 'code_001' );
		$this->assertEquals( 'value_001', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002', 'test_002' );
		$this->assertEquals( 'value_002', $result->getValue() );

		$result = $this->object->getAttributeItem( 'code_002' );
		$this->assertEquals( null, $result );

		$result = $this->object->getAttributeItem( 'code_003' );
		$this->assertEquals( null, $result );

		$this->object->setAttributeItems( [] );

		$result = $this->object->getAttributeItem( 'code_001' );
		$this->assertEquals( null, $result );
	}


	public function testGetAttributeItemList()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$attrItem001 = $attManager->create();
		$attrItem001->setAttributeId( '1' );
		$attrItem001->setCode( 'code_001' );
		$attrItem001->setType( 'test_001' );
		$attrItem001->setValue( 'value_001' );

		$attrItem002 = $attManager->create();
		$attrItem002->setAttributeId( '2' );
		$attrItem002->setCode( 'code_001' );
		$attrItem002->setType( 'test_001' );
		$attrItem002->setValue( 'value_002' );

		$this->object->setAttributeItems( array( $attrItem001, $attrItem002 ) );

		$result = $this->object->getAttributeItem( 'code_001', 'test_001' );
		$this->assertEquals( 2, count( $result ) );
	}


	public function testGetAttributeItems()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributeItems()->toArray() );
	}


	public function testGetAttributeItemsByType()
	{
		$this->assertEquals( $this->attribute, $this->object->getAttributeItems( 'default' )->toArray() );
	}


	public function testGetAttributeItemsInvalidType()
	{
		$this->assertEquals( [], $this->object->getAttributeItems( 'invalid' )->toArray() );
	}


	public function testSetAttributeItem()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'test_value', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value2' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'test_value2', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetAttributeItems()
	{
		$manager = \Aimeos\MShop\Order\Manager\Factory::create( \TestHelperMShop::getContext() );
		$attManager = $manager->getSubManager( 'base' )->getSubManager( 'service' )->getSubManager( 'attribute' );

		$list = array(
			$attManager->create(),
			$attManager->create(),
		);

		$return = $this->object->setAttributeItems( $list );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( $list, $this->object->getAttributeItems()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2012-01-01 00:00:01', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Service\Standard( new \Aimeos\MShop\Price\Item\Standard() );

		$list = $entries = array(
			'order.base.service.id' => 1,
			'order.base.service.baseid' => 2,
			'order.base.service.serviceid' => 3,
			'order.base.service.position' => 4,
			'order.base.service.code' => 'test',
			'order.base.service.name' => 'test item',
			'order.base.service.type' => 'delivery',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.base.service.id'], $item->getId() );
		$this->assertEquals( $list['order.base.service.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.base.service.serviceid'], $item->getServiceId() );
		$this->assertEquals( $list['order.base.service.position'], $item->getPosition() );
		$this->assertEquals( $list['order.base.service.code'], $item->getCode() );
		$this->assertEquals( $list['order.base.service.name'], $item->getName() );
		$this->assertEquals( $list['order.base.service.type'], $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) + 5, count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['order.base.service.id'] );
		$this->assertEquals( $this->object->getBaseId(), $arrayObject['order.base.service.baseid'] );
		$this->assertEquals( $this->object->getServiceId(), $arrayObject['order.base.service.serviceid'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.base.service.position'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['order.base.service.code'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['order.base.service.name'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['order.base.service.type'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.service.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.base.service.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.base.service.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['order.base.service.editor'] );

		$price = $this->object->getPrice();
		$this->assertEquals( $price->getValue(), $arrayObject['order.base.service.price'] );
		$this->assertEquals( $price->getCosts(), $arrayObject['order.base.service.costs'] );
		$this->assertEquals( $price->getRebate(), $arrayObject['order.base.service.rebate'] );
		$this->assertEquals( $price->getTaxRate(), $arrayObject['order.base.service.taxrate'] );
		$this->assertEquals( $price->getTaxRates(), $arrayObject['order.base.service.taxrates'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base/service', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$serviceCopy = new \Aimeos\MShop\Order\Item\Base\Service\Standard( $this->price );

		$manager = \Aimeos\MShop\Service\Manager\Factory::create( \TestHelperMShop::getContext() );

		$filter = $manager->filter()->add( ['service.provider' => 'Standard'] );
		$item = $manager->search( $filter )->first( new \RuntimeException( 'No service found' ) );

		$return = $serviceCopy->copyFrom( $item->set( 'customprop', 123 ) );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $return );
		$this->assertEquals( 'unitdeliverycode', $serviceCopy->getCode() );
		$this->assertEquals( 'unitlabel', $serviceCopy->getName() );
		$this->assertEquals( 'delivery', $serviceCopy->getType() );
		$this->assertEquals( '', $serviceCopy->getMediaUrl() );
		$this->assertEquals( '123', $serviceCopy->get( 'customprop' ) );

		$this->assertTrue( $serviceCopy->isModified() );
	}
}

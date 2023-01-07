<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Item\Service;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;
	private $price;
	private $attributes;
	private $transactions;


	protected function setUp() : void
	{
		$this->price = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create();

		$attrValues = array(
			'order.service.attribute.id' => 3,
			'order.service.attribute.siteid' => 99,
			'order.service.attribute.parentid' => 42,
			'order.service.attribute.name' => 'UnitName',
			'order.service.attribute.type' => 'default',
			'order.service.attribute.code' => 'UnitCode',
			'order.service.attribute.value' => 'UnitValue',
			'order.service.attribute.mtime' => '2020-12-31 23:59:59',
			'order.service.attribute.ctime' => '2011-01-01 00:00:01',
			'order.service.attribute.editor' => 'unitTestUser'
		);

		$this->attributes = [new \Aimeos\MShop\Order\Item\Service\Attribute\Standard( $attrValues )];


		$txValues = array(
			'order.service.transaction.id' => 3,
			'order.service.transaction.siteid' => 99,
			'order.service.transaction.parentid' => 42,
			'order.service.transaction.type' => 'payment',
			'order.service.attribute.config' => [],
			'order.service.attribute.status' => 6,
			'order.service.attribute.mtime' => '2020-12-31 23:59:59',
			'order.service.attribute.ctime' => '2011-01-01 00:00:01',
			'order.service.attribute.editor' => 'unitTestUser'
		);

		$this->transactions = [new \Aimeos\MShop\Order\Item\Service\Transaction\Standard( $this->price, $txValues )];


		$this->values = array(
			'order.service.id' => 1,
			'order.service.siteid' => 99,
			'order.service.serviceid' => 'ServiceID',
			'order.service.parentid' => 42,
			'order.service.code' => 'UnitCode',
			'order.service.name' => 'UnitName',
			'order.service.mediaurl' => 'Url for test',
			'order.service.position' => 1,
			'order.service.type' => 'payment',
			'order.service.mtime' => '2012-01-01 00:00:01',
			'order.service.ctime' => '2011-01-01 00:00:01',
			'order.service.editor' => 'unitTestUser'
		);

		$servItem = \Aimeos\MShop::create( \TestHelper::context(), 'service' )->create();
		$this->object = new \Aimeos\MShop\Order\Item\Service\Standard(
			$this->price, $this->values, $this->attributes, $this->transactions, $servItem
		);
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetServiceItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Service\Item\Iface::class, $this->object->getServiceItem() );
		$this->assertNull( ( new \Aimeos\MShop\Order\Item\Service\Standard( $this->price ) )->getServiceItem() );
	}


	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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


	public function testGetParentId()
	{
		$this->assertEquals( 42, $this->object->getParentId() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 111 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 111, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetParentIdReset()
	{
		$return = $this->object->setParentId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetServiceId()
	{
		$this->assertEquals( 'ServiceID', $this->object->getServiceId() );
	}


	public function testSetServiceId()
	{
		$return = $this->object->setServiceId( 'testServiceID' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 2, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionReset()
	{
		$return = $this->object->setPosition( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( null, $this->object->getPosition() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetPositionInvalid()
	{
		$this->expectException( \Aimeos\MShop\Order\Exception::class );
		$this->object->setPosition( -1 );
	}


	public function testAddAttributeItems()
	{
		$item = \Aimeos\MShop::create( \TestHelper::context(), 'order/service' )->createAttributeItem();

		$return = $this->object->addAttributeItems( [$item] );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 2, count( $this->object->getAttributeItems() ) );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetAttribute()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

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
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

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
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

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
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

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
		$this->assertEquals( $this->attributes, $this->object->getAttributeItems()->toArray() );
	}


	public function testGetAttributeItemsByType()
	{
		$this->assertEquals( $this->attributes, $this->object->getAttributeItems( 'default' )->toArray() );
	}


	public function testGetAttributeItemsInvalidType()
	{
		$this->assertEquals( [], $this->object->getAttributeItems( 'invalid' )->toArray() );
	}


	public function testSetAttributeItem()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 'test_value', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );

		$item = $attManager->create();
		$item->setAttributeId( '1' );
		$item->setCode( 'test_code' );
		$item->setType( 'test_type' );
		$item->setValue( 'test_value2' );

		$return = $this->object->setAttributeItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 'test_value2', $this->object->getAttributeItem( 'test_code', 'test_type' )->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetAttributeItems()
	{
		$attManager = \Aimeos\MShop::create( \TestHelper::context(), 'order/service/attribute' );

		$list = array(
			$attManager->create(),
			$attManager->create(),
		);

		$return = $this->object->setAttributeItems( $list );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( $list, $this->object->getAttributeItems()->toArray() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTransactions()
	{
		$this->assertEquals( $this->transactions, $this->object->getTransactions()->toArray() );
	}


	public function testSetTransactions()
	{
		$txValues = array(
			'order.service.transaction.id' => 5,
			'order.service.transaction.siteid' => 100,
			'order.service.transaction.parentid' => 50,
			'order.service.transaction.type' => 'refund',
			'order.service.attribute.config' => ['tx' => '001'],
			'order.service.attribute.status' => 3,
			'order.service.attribute.mtime' => '2020-12-31 23:59:59',
			'order.service.attribute.ctime' => '2011-01-01 00:00:01',
			'order.service.attribute.editor' => 'unitTestUser'
		);

		$list = [new \Aimeos\MShop\Order\Item\Service\Transaction\Standard( $this->price, $txValues )];
		$result = $this->object->setTransactions( $list );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $result );
		$this->assertEquals( $list, $this->object->getTransactions()->toArray() );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Service\Standard( new \Aimeos\MShop\Price\Item\Standard() );

		$list = $entries = array(
			'order.service.id' => 1,
			'order.service.parentid' => 2,
			'order.service.serviceid' => 3,
			'order.service.position' => 4,
			'order.service.currencyid' => 'EUR',
			'order.service.price' => '1.00',
			'order.service.costs' => '0.50',
			'order.service.rebate' => '0.50',
			'order.service.taxrates' => ['tax' => '20.00'],
			'order.service.taxvalue' => '0.2500',
			'order.service.taxflag' => 1,
			'order.service.code' => 'test',
			'order.service.name' => 'test item',
			'order.service.type' => 'delivery',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.service.id'], $item->getId() );
		$this->assertEquals( $list['order.service.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.service.serviceid'], $item->getServiceId() );
		$this->assertEquals( $list['order.service.position'], $item->getPosition() );
		$this->assertEquals( $list['order.service.currencyid'], $item->getPrice()->getCurrencyId() );
		$this->assertEquals( $list['order.service.price'], $item->getPrice()->getValue() );
		$this->assertEquals( $list['order.service.costs'], $item->getPrice()->getCosts() );
		$this->assertEquals( $list['order.service.rebate'], $item->getPrice()->getRebate() );
		$this->assertEquals( $list['order.service.taxrates'], $item->getPrice()->getTaxRates() );
		$this->assertEquals( $list['order.service.taxvalue'], $item->getPrice()->getTaxValue() );
		$this->assertEquals( $list['order.service.taxflag'], $item->getPrice()->getTaxFlag() );
		$this->assertEquals( $list['order.service.code'], $item->getCode() );
		$this->assertEquals( $list['order.service.name'], $item->getName() );
		$this->assertEquals( $list['order.service.type'], $item->getType() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ) + 8, count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['order.service.id'] );
		$this->assertEquals( $this->object->getParentId(), $arrayObject['order.service.parentid'] );
		$this->assertEquals( $this->object->getServiceId(), $arrayObject['order.service.serviceid'] );
		$this->assertEquals( $this->object->getPosition(), $arrayObject['order.service.position'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['order.service.code'] );
		$this->assertEquals( $this->object->getName(), $arrayObject['order.service.name'] );
		$this->assertEquals( $this->object->getType(), $arrayObject['order.service.type'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['order.service.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['order.service.mtime'] );
		$this->assertEquals( $this->object->editor(), $arrayObject['order.service.editor'] );

		$price = $this->object->getPrice();
		$this->assertEquals( $price->getCurrencyId(), $arrayObject['order.service.currencyid'] );
		$this->assertEquals( $price->getValue(), $arrayObject['order.service.price'] );
		$this->assertEquals( $price->getCosts(), $arrayObject['order.service.costs'] );
		$this->assertEquals( $price->getRebate(), $arrayObject['order.service.rebate'] );
		$this->assertEquals( $price->getTaxRate(), $arrayObject['order.service.taxrate'] );
		$this->assertEquals( $price->getTaxRates(), $arrayObject['order.service.taxrates'] );
		$this->assertEquals( $price->getTaxValue(), $arrayObject['order.service.taxvalue'] );
		$this->assertEquals( $price->getTaxFlag(), $arrayObject['order.service.taxflag'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/service', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$serviceCopy = new \Aimeos\MShop\Order\Item\Service\Standard( $this->price );

		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'service' );

		$filter = $manager->filter()->add( ['service.provider' => 'Standard'] );
		$item = $manager->search( $filter )->first( new \RuntimeException( 'No service found' ) );

		$return = $serviceCopy->copyFrom( $item->set( 'customprop', 123 ) );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Service\Iface::class, $return );
		$this->assertEquals( 'unitdeliverycode', $serviceCopy->getCode() );
		$this->assertEquals( 'unitlabel', $serviceCopy->getName() );
		$this->assertEquals( 'delivery', $serviceCopy->getType() );
		$this->assertEquals( '', $serviceCopy->getMediaUrl() );
		$this->assertEquals( '123', $serviceCopy->get( 'customprop' ) );

		$this->assertTrue( $serviceCopy->isModified() );
	}
}

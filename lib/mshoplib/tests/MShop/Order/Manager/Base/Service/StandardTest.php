<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Manager\Base\Service;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Service\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.service.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.service.code' )->toArray();

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( 'unitpaymentcode', $result );
		$this->assertEquals( 2, $result['unitpaymentcode'] );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.service.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.service.type', 'order.base.service.costs', 'avg' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'delivery', $result );
		$this->assertEquals( '2.50', round( $result['delivery'], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.service.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.service.type', 'order.base.service.costs', 'sum' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'delivery', $result );
		$this->assertEquals( '10.00', $result['delivery'] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/base/service', $result );
		$this->assertContains( 'order/base/service/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute )
		{
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$actual = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Iface::class, $actual );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter( true ) );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.base.service.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.baseid', null );
		$expr[] = $search->compare( '!=', 'order.base.service.serviceid', null );
		$expr[] = $search->compare( '==', 'order.base.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.base.service.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.base.service.taxflag', 1 );
		$expr[] = $search->compare( '==', 'order.base.service.taxvalue', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.position', 0 );
		$expr[] = $search->compare( '>=', 'order.base.service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.quantity', 1 );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemRef()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'order.base.service.code', 'unitpaymentcode' ) );
		$result = $this->object->search( $search, ['service'] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNotNull( $result->first()->getServiceItem() );
	}


	public function testSearchItemTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );

		$search->setConditions( $search->and( [
			$search->compare( '==', 'order.base.service.code', array( 'unitpaymentcode', 'not exists' ) ),
			$search->compare( '==', 'order.base.service.editor', $this->editor )
		] ) );

		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'attribute' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'attribute', 'Standard' ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'attribute', 'unknown' );
	}


	public function testGetSubManagerInvalidType()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( '$$$' );
	}


	public function testGetSubManagerInvalidDefaultName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'attribute', '$$$' );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.base.service.code', 'unitpaymentcode' ),
			$search->compare( '==', 'order.base.service.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( !( $item = reset( $results ) ) ) {
			throw new \RuntimeException( 'empty results' );
		}

		$actual = $this->object->get( $item->getId() );
		$this->assertEquals( $item, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.base.service.code', 'unitpaymentcode' ),
			$search->compare( '==', 'order.base.service.editor', $this->editor ),
			$search->compare( '==', 'order.base.service.attribute.code', 'NAME' ),
		);
		$search->setConditions( $search->and( $conditions ) );
		$orderItems = $this->object->search( $search )->toArray();

		if( !( $item = reset( $orderItems ) ) ) {
			throw new \RuntimeException( 'empty search result' );
		}

		$item->setId( null );
		$item->setCode( 'unittest1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );


		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unittest1' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getServiceId(), $itemSaved->getServiceId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getMediaUrl(), $itemSaved->getMediaUrl() );
		$this->assertEquals( $item->getPrice()->getValue(), $itemSaved->getPrice()->getValue() );
		$this->assertEquals( $item->getPrice()->getCosts(), $itemSaved->getPrice()->getCosts() );
		$this->assertEquals( $item->getPrice()->getRebate(), $itemSaved->getPrice()->getRebate() );
		$this->assertEquals( $item->getPrice()->getTaxflag(), $itemSaved->getPrice()->getTaxflag() );
		$this->assertEquals( $item->getPrice()->getTaxValue(), $itemSaved->getPrice()->getTaxValue() );
		$this->assertNotEquals( [], $item->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId() );
		$this->assertEquals( $itemExp->getServiceId(), $itemUpd->getServiceID() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getMediaUrl(), $itemUpd->getMediaUrl() );
		$this->assertEquals( $itemExp->getPrice()->getValue(), $itemUpd->getPrice()->getValue() );
		$this->assertEquals( $itemExp->getPrice()->getCosts(), $itemUpd->getPrice()->getCosts() );
		$this->assertEquals( $itemExp->getPrice()->getRebate(), $itemUpd->getPrice()->getRebate() );
		$this->assertEquals( $itemExp->getPrice()->getTaxflag(), $itemUpd->getPrice()->getTaxflag() );
		$this->assertEquals( $itemExp->getPrice()->getTaxValue(), $itemUpd->getPrice()->getTaxValue() );
		$this->assertNotEquals( [], $itemUpd->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'order.type' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'web', $result );
		$this->assertEquals( 3, $result->get( 'web' ) );
	}


	public function testAggregateMultiple()
	{
		$cols = ['order.type', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols );

		$this->assertEquals( ['phone' => [6 => 1], 'web' => [5 => 1, 6 => 2]], $result->toArray() );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'order.cmonth', 'order.base.price', 'avg' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( '784.75', round( $result->first(), 2 ) );
	}


	public function testAggregateAvgMultiple()
	{
		$cols = ['order.cmonth', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols, 'order.base.price', 'avg' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 5, $result->first() );
		$this->assertArrayHasKey( 6, $result->first() );
		$this->assertEquals( '13.50', round( $result->first()[5], 2 ) );
		$this->assertEquals( '1041.83', round( $result->first()[6], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'order.cmonth', 'order.base.price', 'sum' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( '3139.00', $result->first() );
	}


	public function testAggregateSumMultiple()
	{
		$cols = ['order.cmonth', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols, 'order.base.price', 'sum' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 5, $result->first() );
		$this->assertArrayHasKey( 6, $result->first() );
		$this->assertEquals( '13.50', round( $result->first()[5], 2 ) );
		$this->assertEquals( '3125.5', round( $result->first()[6], 2 ) );
	}


	public function testAggregateTimes()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$search->setSortations( array( $search->sort( '-', 'order.cdate' ) ) );
		$result = $this->object->aggregate( $search, 'order.cmonth' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateAddress()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'order.base.address.countryid' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'DE', $result );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateAddressMultiple()
	{
		$cols = ['order.base.address.countryid', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols )->toArray();

		$this->assertEquals( ['DE' => [5 => 1, 6 => 3]], $result );
	}


	public function testAggregateMonth()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core:lib/mshoplib'] );
		$result = $this->object->aggregate( $search, 'order.type' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'web', $result );
		$this->assertEquals( 3, $result['web'] );
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

		$this->assertContains( 'order', $result );
		$this->assertContains( 'order/status', $result );
		$this->assertContains( 'order/base', $result );
		$this->assertContains( 'order/base/address', $result );
		$this->assertContains( 'order/base/coupon', $result );
		$this->assertContains( 'order/base/product', $result );
		$this->assertContains( 'order/base/product/attribute', $result );
		$this->assertContains( 'order/base/service', $result );
		$this->assertContains( 'order/base/service/attribute', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$status = \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED;

		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.statuspayment', $status ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order found in shop_order_invoice with statuspayment "%1$s"', $status ) );
		}

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order item found' ) );

		$item->setId( null );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setType( \Aimeos\MShop\Order\Item\Base::TYPE_WEB );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getDatePayment(), $itemSaved->getDatePayment() );
		$this->assertEquals( $item->getDateDelivery(), $itemSaved->getDateDelivery() );
		$this->assertEquals( $item->getStatusPayment(), $itemSaved->getStatusPayment() );
		$this->assertEquals( $item->getStatusDelivery(), $itemSaved->getStatusDelivery() );
		$this->assertEquals( $item->getRelatedId(), $itemSaved->getRelatedId() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getDatePayment(), $itemUpd->getDatePayment() );
		$this->assertEquals( $itemExp->getDateDelivery(), $itemUpd->getDateDelivery() );
		$this->assertEquals( $itemExp->getStatusPayment(), $itemUpd->getStatusPayment() );
		$this->assertEquals( $itemExp->getStatusDelivery(), $itemUpd->getStatusDelivery() );
		$this->assertEquals( $itemExp->getRelatedId(), $itemUpd->getRelatedId() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testSaveStatusUpdatePayment()
	{
		$statusManager = \Aimeos\MShop::create( $this->context, 'order/status' );

		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order item found.' );
		}

		$item->setId( null );
		$this->object->save( $item );


		$search = $statusManager->filter();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->search( $search )->toArray();

		$this->object->delete( $item->getId() );

		$this->assertEquals( 0, count( $results ) );


		$item->setId( null );
		$item->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
		$this->object->save( $item );

		$search = $statusManager->filter();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->search( $search )->toArray();

		$this->object->delete( $item->getId() );

		if( ( $statusItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No status item found' );
		}

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Status\Base::STATUS_PAYMENT, $statusItem->getType() );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $statusItem->getValue() );
	}


	public function testSaveStatusUpdateDelivery()
	{
		$statusManager = \Aimeos\MShop::create( $this->context, 'order/status' );

		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order item found.' );
		}

		$item->setId( null );
		$this->object->save( $item );


		$search = $statusManager->filter();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->search( $search )->toArray();

		$this->object->delete( $item->getId() );

		$this->assertEquals( 0, count( $results ) );


		$item->setId( null );
		$item->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_LOST );
		$this->object->save( $item );

		$search = $statusManager->filter();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->search( $search )->toArray();

		$this->object->delete( $item->getId() );

		if( ( $statusItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No status item found' );
		}

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Status\Base::STATUS_DELIVERY, $statusItem->getType() );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_LOST, $statusItem->getValue() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testCreateSearchDefault()
	{
		$result = $this->object->filter( true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testCreateSearchSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();
		$funcStatus = $search->make( 'order:status', ['typestatus', 'shipped'] );

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.id', null );
		$expr[] = $search->compare( '==', 'order.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.baseid', null );
		$expr[] = $search->compare( '==', 'order.type', 'web' );
		$expr[] = $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' );
		$expr[] = $search->compare( '==', 'order.datedelivery', null );
		$expr[] = $search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
		$expr[] = $search->compare( '==', 'order.statusdelivery', 4 );
		$expr[] = $search->compare( '==', 'order.relatedid', '' );
		$expr[] = $search->compare( '>=', 'order.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.editor', $this->editor );
		$expr[] = $search->compare( '==', $funcStatus, 1 );

		$expr[] = $search->compare( '!=', 'order.status.id', null );
		$expr[] = $search->compare( '==', 'order.status.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.status.parentid', null );
		$expr[] = $search->compare( '>=', 'order.status.type', 'typestatus' );
		$expr[] = $search->compare( '==', 'order.status.value', 'shipped' );
		$expr[] = $search->compare( '>=', 'order.status.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.status.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.status.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.id', null );
		$expr[] = $search->compare( '==', 'order.base.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '>=', 'order.base.customerid', '' );
		$expr[] = $search->compare( '==', 'order.base.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'order.base.price', '53.50' );
		$expr[] = $search->compare( '==', 'order.base.costs', '1.50' );
		$expr[] = $search->compare( '==', 'order.base.rebate', '14.50' );
		$expr[] = $search->compare( '~=', 'order.base.comment', 'This is a comment' );
		$expr[] = $search->compare( '>=', 'order.base.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.address.id', null );
		$expr[] = $search->compare( '==', 'order.base.address.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.address.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.address.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.address.company', 'Example company' );
		$expr[] = $search->compare( '==', 'order.base.address.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'order.base.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'order.base.address.title', '' );
		$expr[] = $search->compare( '==', 'order.base.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'order.base.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'order.base.address.address1', 'Durchschnitt' );
		$expr[] = $search->compare( '==', 'order.base.address.address2', '1' );
		$expr[] = $search->compare( '==', 'order.base.address.address3', '' );
		$expr[] = $search->compare( '==', 'order.base.address.postal', '20146' );
		$expr[] = $search->compare( '==', 'order.base.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.base.address.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.base.address.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'order.base.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'order.base.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'order.base.address.telefax', '055544332213' );
		$expr[] = $search->compare( '==', 'order.base.address.website', 'www.example.net' );
		$expr[] = $search->compare( '>=', 'order.base.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.address.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.coupon.id', null );
		$expr[] = $search->compare( '==', 'order.base.coupon.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.coupon.baseid', null );
		$expr[] = $search->compare( '!=', 'order.base.coupon.productid', null );
		$expr[] = $search->compare( '==', 'order.base.coupon.code', 'OPQR' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.editor', '' );

		$expr[] = $search->compare( '!=', 'order.base.product.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.baseid', null );
		$expr[] = $search->compare( '!=', 'order.base.product.productid', null );
		$expr[] = $search->compare( '==', 'order.base.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.base.product.supplierid', 'unitSupplier001' );
		$expr[] = $search->compare( '==', 'order.base.product.suppliername', 'Test supplier' );
		$expr[] = $search->compare( '==', 'order.base.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.base.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.base.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.base.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.base.product.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.base.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.base.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.statuspayment', 5 );
		$expr[] = $search->compare( '==', 'order.base.product.statusdelivery', 1 );
		$expr[] = $search->compare( '>=', 'order.base.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.quantity', 1 );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.service.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.base.service.taxrates', '{' );
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


	public function testSearchItemsTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$items = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 3, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsRef()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$item = $this->object->search( $search, ['order/base', 'order/base/product'], $total )->first();

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $item->getBaseItem() );
		$this->assertEquals( 4, count( $item->getBaseItem()->getProducts() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'base' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'base', 'Standard' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'status' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'status', 'Standard' ) );


		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'base', 'unknown' );
	}

}

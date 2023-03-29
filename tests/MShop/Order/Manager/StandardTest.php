<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Order\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$result = $this->object->aggregate( $search, 'order.channel' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'web', $result );
		$this->assertEquals( 3, $result->get( 'web' ) );
	}


	public function testAggregateMultiple()
	{
		$cols = ['order.channel', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols );

		$this->assertEquals( ['phone' => [6 => 1], 'web' => [5 => 1, 6 => 2]], $result->toArray() );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$result = $this->object->aggregate( $search, 'order.cmonth', 'order.price', 'avg' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( '784.75', round( $result->first(), 2 ) );
	}


	public function testAggregateAvgMultiple()
	{
		$cols = ['order.cmonth', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols, 'order.price', 'avg' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 5, $result->first() );
		$this->assertArrayHasKey( 6, $result->first() );
		$this->assertEquals( '13.50', round( $result->first()[5], 2 ) );
		$this->assertEquals( '1041.83', round( $result->first()[6], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$result = $this->object->aggregate( $search, 'order.cmonth', 'order.price', 'sum' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( '3139.00', $result->first() );
	}


	public function testAggregateSumMultiple()
	{
		$cols = ['order.cmonth', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols, 'order.price', 'sum' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 5, $result->first() );
		$this->assertArrayHasKey( 6, $result->first() );
		$this->assertEquals( '13.50', round( $result->first()[5], 2 ) );
		$this->assertEquals( '3125.5', round( $result->first()[6], 2 ) );
	}


	public function testAggregateTimes()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$search->setSortations( array( $search->sort( '-', 'order.cdate' ) ) );
		$result = $this->object->aggregate( $search, 'order.cmonth' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateAddress()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$result = $this->object->aggregate( $search, 'order.address.countryid' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'DE', $result );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateAddressMultiple()
	{
		$cols = ['order.address.countryid', 'order.statuspayment'];
		$search = $this->object->filter()->add( ['order.editor' => 'core'] )->order( $cols );
		$result = $this->object->aggregate( $search, $cols )->toArray();

		$this->assertEquals( ['DE' => [5 => 1, 6 => 3]], $result );
	}


	public function testAggregateMonth()
	{
		$search = $this->object->filter()->add( ['order.editor' => 'core'] );
		$result = $this->object->aggregate( $search, 'order.channel' )->toArray();

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
		$this->assertContains( 'order/address', $result );
		$this->assertContains( 'order/coupon', $result );
		$this->assertContains( 'order/product', $result );
		$this->assertContains( 'order/product/attribute', $result );
		$this->assertContains( 'order/service', $result );
		$this->assertContains( 'order/service/attribute', $result );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 )
			->add( ['order.price' => '672.00', 'order.editor' => $this->editor] );

		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order item found' ) );

		$actual = $this->object->get( $item->getId() );

		$this->assertEquals( $item, $actual );
		$this->assertEquals( '32.00', $item->getPrice()->getCosts() );
		$this->assertEquals( '5.00', $item->getPrice()->getRebate() );
		$this->assertEquals( '112.4034', $item->getPrice()->getTaxValue() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter()->slice( 0, 1 )
			->add( ['order.channel' => 'phone', 'order.editor' => $this->editor] );

		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order item found' ) );

		$item->setId( null );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setChannel( 'web' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$itemPrice = $item->getPrice();
		$itemSavedPrice = $itemSaved->getPrice();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getChannel(), $itemSaved->getChannel() );
		$this->assertEquals( $item->getDatePayment(), $itemSaved->getDatePayment() );
		$this->assertEquals( $item->getDateDelivery(), $itemSaved->getDateDelivery() );
		$this->assertEquals( $item->getStatusPayment(), $itemSaved->getStatusPayment() );
		$this->assertEquals( $item->getStatusDelivery(), $itemSaved->getStatusDelivery() );
		$this->assertEquals( $item->getInvoiceNumber(), $itemSaved->getInvoiceNumber() );
		$this->assertEquals( $item->getRelatedId(), $itemSaved->getRelatedId() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->locale()->getLanguageId(), $itemSaved->locale()->getLanguageId() );
		$this->assertEquals( $item->getCustomerReference(), $itemSaved->getCustomerReference() );
		$this->assertEquals( $item->getComment(), $itemSaved->getComment() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );
		$this->assertEquals( $itemPrice->getValue(), $itemSavedPrice->getValue() );
		$this->assertEquals( $itemPrice->getCosts(), $itemSavedPrice->getCosts() );
		$this->assertEquals( $itemPrice->getRebate(), $itemSavedPrice->getRebate() );
		$this->assertEquals( $itemPrice->getTaxValue(), $itemSavedPrice->getTaxValue() );
		$this->assertEquals( $itemPrice->getCurrencyId(), $itemSavedPrice->getCurrencyId() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$itemExpPrice = $itemExp->getPrice();
		$itemUpdPrice = $itemUpd->getPrice();

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getChannel(), $itemUpd->getChannel() );
		$this->assertEquals( $itemExp->getDatePayment(), $itemUpd->getDatePayment() );
		$this->assertEquals( $itemExp->getDateDelivery(), $itemUpd->getDateDelivery() );
		$this->assertEquals( $itemExp->getStatusPayment(), $itemUpd->getStatusPayment() );
		$this->assertEquals( $itemExp->getStatusDelivery(), $itemUpd->getStatusDelivery() );
		$this->assertEquals( $itemExp->getInvoiceNumber(), $itemUpd->getInvoiceNumber() );
		$this->assertEquals( $itemExp->getRelatedId(), $itemUpd->getRelatedId() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->locale()->getLanguageId(), $itemUpd->locale()->getLanguageId() );
		$this->assertEquals( $itemExp->getCustomerReference(), $itemUpd->getCustomerReference() );
		$this->assertEquals( $itemExp->getComment(), $itemUpd->getComment() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );
		$this->assertEquals( $itemExpPrice->getValue(), $itemUpdPrice->getValue() );
		$this->assertEquals( $itemExpPrice->getCosts(), $itemUpdPrice->getCosts() );
		$this->assertEquals( $itemExpPrice->getRebate(), $itemUpdPrice->getRebate() );
		$this->assertEquals( $itemExpPrice->getTaxValue(), $itemUpdPrice->getTaxValue() );
		$this->assertEquals( $itemExpPrice->getCurrencyId(), $itemUpdPrice->getCurrencyId() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

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
			$search->compare( '==', 'order.channel', 'phone' ),
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
			$search->compare( '==', 'order.channel', 'phone' ),
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
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testCreateSearchDefault()
	{
		$search = $this->object->filter( true );

		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $search );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $search->getConditions() );

		$list = $search->getConditions()->getExpressions();
		$this->assertArrayHasKey( 0, $list );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $list[0] );
	}


	public function testCreateSearchSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->locale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();
		$funcStatus = $search->make( 'order:status', ['typestatus', 'shipped'] );

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.id', null );
		$expr[] = $search->compare( '==', 'order.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.channel', 'web' );
		$expr[] = $search->compare( '==', 'order.invoiceno', 'UINV-001' );
		$expr[] = $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' );
		$expr[] = $search->compare( '==', 'order.datedelivery', null );
		$expr[] = $search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
		$expr[] = $search->compare( '==', 'order.statusdelivery', 4 );
		$expr[] = $search->compare( '==', 'order.relatedid', '' );
		$expr[] = $search->compare( '==', 'order.sitecode', 'unittest' );
		$expr[] = $search->compare( '>=', 'order.customerid', '' );
		$expr[] = $search->compare( '==', 'order.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'order.price', '53.50' );
		$expr[] = $search->compare( '==', 'order.costs', '1.50' );
		$expr[] = $search->compare( '==', 'order.rebate', '14.50' );
		$expr[] = $search->compare( '~=', 'order.comment', 'This is a comment' );
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

		$expr[] = $search->compare( '!=', 'order.address.id', null );
		$expr[] = $search->compare( '==', 'order.address.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.address.parentid', null );
		$expr[] = $search->compare( '==', 'order.address.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.address.company', 'Example company' );
		$expr[] = $search->compare( '==', 'order.address.vatid', 'DE999999999' );
		$expr[] = $search->compare( '==', 'order.address.salutation', 'mr' );
		$expr[] = $search->compare( '==', 'order.address.title', '' );
		$expr[] = $search->compare( '==', 'order.address.firstname', 'Our' );
		$expr[] = $search->compare( '==', 'order.address.lastname', 'Unittest' );
		$expr[] = $search->compare( '==', 'order.address.address1', 'Durchschnitt' );
		$expr[] = $search->compare( '==', 'order.address.address2', '1' );
		$expr[] = $search->compare( '==', 'order.address.address3', '' );
		$expr[] = $search->compare( '==', 'order.address.postal', '20146' );
		$expr[] = $search->compare( '==', 'order.address.city', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.address.state', 'Hamburg' );
		$expr[] = $search->compare( '==', 'order.address.countryid', 'DE' );
		$expr[] = $search->compare( '==', 'order.address.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.address.telephone', '055544332211' );
		$expr[] = $search->compare( '==', 'order.address.email', 'test@example.com' );
		$expr[] = $search->compare( '==', 'order.address.telefax', '055544332213' );
		$expr[] = $search->compare( '==', 'order.address.website', 'www.example.net' );
		$expr[] = $search->compare( '>=', 'order.address.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.address.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.address.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.coupon.id', null );
		$expr[] = $search->compare( '==', 'order.coupon.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.coupon.parentid', null );
		$expr[] = $search->compare( '!=', 'order.coupon.productid', null );
		$expr[] = $search->compare( '==', 'order.coupon.code', 'OPQR' );
		$expr[] = $search->compare( '>=', 'order.coupon.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.coupon.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.coupon.editor', '' );

		$expr[] = $search->compare( '!=', 'order.product.id', null );
		$expr[] = $search->compare( '==', 'order.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.product.parentid', null );
		$expr[] = $search->compare( '!=', 'order.product.productid', null );
		$expr[] = $search->compare( '==', 'order.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.product.vendor', 'Test vendor' );
		$expr[] = $search->compare( '==', 'order.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.product.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.product.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.product.statuspayment', 5 );
		$expr[] = $search->compare( '==', 'order.product.statusdelivery', 1 );
		$expr[] = $search->compare( '>=', 'order.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.product.attribute.name', '33' );
		$expr[] = $search->compare( '==', 'order.product.attribute.quantity', 1 );
		$expr[] = $search->compare( '>=', 'order.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.product.attribute.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.service.id', null );
		$expr[] = $search->compare( '==', 'order.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.service.parentid', null );
		$expr[] = $search->compare( '==', 'order.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.service.code', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.service.name', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.service.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.service.taxrates', '{' );
		$expr[] = $search->compare( '>=', 'order.service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.service.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.service.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '==', 'order.service.attribute.quantity', 1 );
		$expr[] = $search->compare( '>=', 'order.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.service.attribute.editor', $this->editor );



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
		$item = $this->object->search( $search, ['order/address', 'order/coupon', 'order/product', 'order/service'], $total )->first();

		$this->assertEquals( 2, count( $item->getAddresses() ) );
		$this->assertEquals( 2, count( $item->getCoupons() ) );
		$this->assertEquals( 4, count( $item->getProducts() ) );
		$this->assertEquals( 2, count( $item->getServices() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'address', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'coupon' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'coupon', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'product' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'product', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'service' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'service', 'Standard' ) );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'status' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'status', 'Standard' ) );

		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'status', 'unknown' );
	}


	public function testSave()
	{
		$item = $this->getOrderItem();
		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];

		$basket = $this->getBasket( $item->getId(), $ref, true );
		$this->object->save( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->getBasket( $newBasketId, $ref );
		$this->object->delete( $newBasketId );


		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->locale()->getSiteId(), $basket->getSiteId() );

		$this->assertEquals( 1.50, $basket->getPrice()->getCosts() );

		$pos = 1;
		$products = $basket->getProducts();
		$this->assertEquals( 4, count( $products ) );

		foreach( $products as $product )
		{
			if( $product->getProductCode() != 'U:MD' ) {
				$this->assertGreaterThanOrEqual( 2, count( $product->getAttributeItems() ) );
			}
			$this->assertEquals( $pos++, $product->getPosition() );
		}

		$this->assertEquals( 2, count( $basket->getAddresses() ) );

		$services = $basket->getServices();
		$this->assertEquals( 2, count( $services ) );

		$attributes = [];
		foreach( $services as $list )
		{
			foreach( $list as $service ) {
				$attributes[$service->getCode()] = $service->getAttributeItems();
			}
		}

		$this->assertEquals( 9, count( $attributes['unitpaymentcode'] ) );
		$this->assertEquals( 0, count( $attributes['unitdeliverycode'] ) );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $newBasketId );
	}


	public function testSaveExisting()
	{
		$item = $this->getOrderItem();
		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];

		$basket = $this->getBasket( $item->getId(), $ref, true );
		$this->object->save( $basket );
		$newBasketId = $basket->getId();
		$this->object->save( $basket );
		$newBasket = $this->getBasket( $newBasketId, $ref );

		$this->object->delete( $newBasketId );

		foreach( $basket->getAddresses() as $type => $list )
		{
			$this->assertTrue( map( $list )->getId()->equals( map( $newBasket->getAddress( $type ) )->getId() ) );
		}

		$this->assertTrue( $basket->getProducts()->getId()->equals( $newBasket->getProducts()->getId() ) );

		foreach( $basket->getServices() as $type => $list )
		{
			$this->assertTrue( map( $list )->getId()->equals( map( $newBasket->getService( $type ) )->getId() ) );
		}
	}


	public function testSaveBundles()
	{
		$search = $this->object->filter()->add( ['order.sitecode' => 'unittest', 'order.price' => 2400.00] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );

		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];
		$basket = $this->getBasket( $item->getId(), $ref, true );
		$this->object->save( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->getBasket( $newBasketId, $ref );
		$this->object->delete( $newBasketId );

		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->locale()->getSiteId(), $basket->getSiteId() );

		$pos = 1;
		$products = $basket->getProducts();

		$this->assertEquals( 2, count( $products ) );
		foreach( $products as $product )
		{
			$this->assertEquals( 2, count( $product->getProducts() ) );
			$this->assertEquals( $pos, $product->getPosition() );
			$pos += 3; // two sub-products in between
		}

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $newBasketId );
	}


	public function testSaveAddress()
	{
		$item = $this->getOrderItem();

		$basket = $this->getBasket( $item->getId(), ['order/address'], true );
		$this->object->save( $basket );

		$newBasketId = $basket->getId();

		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];
		$basket = $this->getBasket( $newBasketId, $ref );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getAddresses() ) );
		$this->assertEquals( [], $basket->getProducts()->toArray() );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
		$this->assertEquals( [], $basket->getServices()->toArray() );
	}


	public function testSaveProduct()
	{
		$item = $this->getOrderItem();

		$basket = $this->getBasket( $item->getId(), ['order/product'], true );
		$this->object->save( $basket );

		$newBasketId = $basket->getId();

		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];
		$basket = $this->getBasket( $newBasketId, $ref );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getProducts() ) );
		$this->assertEquals( [], $basket->getAddresses()->toArray() );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
		$this->assertEquals( [], $basket->getServices()->toArray() );
	}


	public function testSaveService()
	{
		$item = $this->getOrderItem();

		$basket = $this->getBasket( $item->getId(), ['order/service'], true );
		$this->object->save( $basket );

		$newBasketId = $basket->getId();

		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];
		$basket = $this->getBasket( $newBasketId, $ref );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getServices() ) );
		$this->assertEquals( [], $basket->getProducts()->toArray() );
		$this->assertEquals( [], $basket->getAddresses()->toArray() );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
	}


	public function testLoadSaveCoupons()
	{
		$ref = ['order/address', 'order/product', 'order/service'];

		$search = $this->object->filter()->add( ['order.price' => '53.50'] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );

		$basket = $this->getBasket( $item->getId(), $ref, true );

		$this->assertEquals( '53.50', $basket->getPrice()->getValue() );
		$this->assertEquals( '1.50', $basket->getPrice()->getCosts() );
		$this->assertEquals( 0, count( $basket->getCoupons() ) );

		$basket->addCoupon( 'CDEF' );
		$basket->addCoupon( '90AB' );
		$this->assertEquals( 2, count( $basket->getCoupons() ) );

		$this->object->save( $basket );

		$ref = ['order/address', 'order/coupon', 'order/product', 'order/service'];

		$newBasket = $this->object->get( $basket->getId(), $ref );
		$this->object->delete( $newBasket->getId() );

		$this->assertEquals( '53.50', $newBasket->getPrice()->getValue() );
		$this->assertEquals( '1.50', $newBasket->getPrice()->getCosts() );
		$this->assertEquals( '14.50', $newBasket->getPrice()->getRebate() );
		$this->assertEquals( 2, count( $newBasket->getCoupons() ) );
	}


	/**
	 * Returns the basket object
	 *
	 * @param string|null $id Unique order ID
	 * @param array $ref List of items that should be fetched too
	 * @param bool $fresh TRUE to return items without IDs
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item
	 * @throws \Exception If no found
	 */
	protected function getBasket( ?string $id, array $ref = [], bool $fresh = false )
	{
		if( $id === null ) {
			throw new \Exception( 'ID can not be NULL' );
		}

		$basket = $this->object->get( $id, $ref );

		if( $fresh )
		{
			$basket->setId( null );

			$basket->getAddresses()->flat( 1 )->setParentId( null )->setId( null );
			$basket->getServices()->flat( 1 )->setParentId( null )->setId( null );

			$basket->getProducts()->merge( $basket->getProducts()->getProducts()->flat( 1 ) )
				->setParentId( null )->setPosition( null )->setId( null );
		}

		return $basket;
	}


	/**
	 * Returns an order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Iface Order base item
	 * @throws \Exception If no found
	 */
	protected function getOrderItem()
	{
		$search = $this->object->filter()->add( [
			'order.sitecode' => 'unittest',
			'order.price' => 53.50,
			'order.rebate' => 14.50,
			'order.editor' => $this->editor
		] );

		return $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );
	}
}

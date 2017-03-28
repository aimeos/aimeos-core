<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Test class for \Aimeos\MShop\Order\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Standard( $this->context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.type' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'web', $result );
		$this->assertEquals( 3, $result['web'] );
	}


	public function testAggregateTimes()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.editor', 'core:unittest' ) );
		$search->setSortations( array( $search->sort( '-', 'order.cdate' ) ) );
		$result = $this->object->aggregate( $search, 'order.cmonth' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateAddress()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.base.address.countryid' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'DE', $result );
		$this->assertEquals( 4, reset( $result ) );
	}


	public function testAggregateMonth()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.type' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'web', $result );
		$this->assertEquals( 3, $result['web'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Iface', $this->object->createItem() );
	}


	public function testGetItem()
	{
		$status = \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED;

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.statuspayment', $status ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order found in shop_order_invoice with statuspayment "%1$s"', $status ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Order\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order item found.' );
		}

		$item->setId( null );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setType( \Aimeos\MShop\Order\Item\Base::TYPE_WEB );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getDatePayment(), $itemSaved->getDatePayment() );
		$this->assertEquals( $item->getDateDelivery(), $itemSaved->getDateDelivery() );
		$this->assertEquals( $item->getPaymentStatus(), $itemSaved->getPaymentStatus() );
		$this->assertEquals( $item->getDeliveryStatus(), $itemSaved->getDeliveryStatus() );
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
		$this->assertEquals( $itemExp->getPaymentStatus(), $itemUpd->getPaymentStatus() );
		$this->assertEquals( $itemExp->getDeliveryStatus(), $itemUpd->getDeliveryStatus() );
		$this->assertEquals( $itemExp->getRelatedId(), $itemUpd->getRelatedId() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSaveStatusUpdatePayment()
	{
		$statusManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/status' );

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order item found.' );
		}

		$item->setId( null );
		$this->object->saveItem( $item );


		$search = $statusManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->searchItems( $search );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 0, count( $results ) );


		$item->setId( null );
		$item->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED );
		$this->object->saveItem( $item );

		$search = $statusManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->searchItems( $search );

		$this->object->deleteItem( $item->getId() );

		if( ( $statusItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No status item found' );
		}

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Status\Base::STATUS_PAYMENT, $statusItem->getType() );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_CANCELED, $statusItem->getValue() );
	}


	public function testSaveStatusUpdateDelivery()
	{
		$statusManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/status' );

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.type', \Aimeos\MShop\Order\Item\Base::TYPE_PHONE ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order item found.' );
		}

		$item->setId( null );
		$this->object->saveItem( $item );


		$search = $statusManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->searchItems( $search );

		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 0, count( $results ) );


		$item->setId( null );
		$item->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_LOST );
		$this->object->saveItem( $item );

		$search = $statusManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.status.parentid', $item->getId() ) );
		$results = $statusManager->searchItems( $search );

		$this->object->deleteItem( $item->getId() );

		if( ( $statusItem = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No status item found' );
		}

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Status\Base::STATUS_DELIVERY, $statusItem->getType() );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_LOST, $statusItem->getValue() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$param = array( (string) \Aimeos\MShop\Order\Item\Status\Base::STATUS_PAYMENT, (string) \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
		$funcStatPayment = $search->createFunction( 'order.containsStatus', $param );

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.id', null );
		$expr[] = $search->compare( '==', 'order.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.baseid', null );
		$expr[] = $search->compare( '==', 'order.type', 'web' );
		$expr[] = $search->compare( '==', 'order.datepayment', '2008-02-15 12:34:56' );
		$expr[] = $search->compare( '==', 'order.datedelivery', null );
		$expr[] = $search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED );
		$expr[] = $search->compare( '==', 'order.statusdelivery', 4 );
		$expr[] = $search->compare( '==', 'order.relatedid', null );
		$expr[] = $search->compare( '>=', 'order.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.editor', $this->editor );
		$expr[] = $search->compare( '==', $funcStatPayment, 1 );

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
		$expr[] = $search->compare( '==', 'order.base.address.company', '' );
		$expr[] = $search->compare( '==', 'order.base.address.vatid', '' );
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
		$expr[] = $search->compare( '==', 'order.base.address.website', 'www.metaways.net' );
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
		$expr[] = $search->compare( '==', 'order.base.product.suppliercode', 'unitsupplier' );
		$expr[] = $search->compare( '==', 'order.base.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.base.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.base.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.base.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.taxrate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.base.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.status', 1 );
		$expr[] = $search->compare( '>=', 'order.base.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.service.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.service.type', 'payment' );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'OGONE' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'ogone' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.taxrate', '0.00' );
		$expr[] = $search->compare( '>=', 'order.base.service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.service.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.service.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.code', 'NAME' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.value', '"CreditCard"' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.service.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.service.attribute.editor', $this->editor );



		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.statuspayment', \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED ),
			$search->compare( '==', 'order.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 3, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'base' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'base', 'Standard' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'status' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'status', 'Standard' ) );


		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'base', 'unknown' );
	}

}

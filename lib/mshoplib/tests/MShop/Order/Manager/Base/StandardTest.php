<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Manager\Base;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();

		$this->object = new \Aimeos\MShop\Order\Manager\Base\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.rebate' )->toArray();

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( '5.00', $result );
		$this->assertEquals( 1, $result['5.00'] );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.address.email', 'order.base.price', 'avg' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'test@example.com', $result );
		$this->assertEquals( '784.75', round( $result['test@example.com'], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.address.email', 'order.base.price', 'sum' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( 'test@example.com', $result );
		$this->assertEquals( '3139.00', $result['test@example.com'] );
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
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $this->object->create() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'order.base.price', '672.00' ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Aimeos\MShop\Order\Exception( 'No order base item found' );
		}

		$item = $this->object->get( $expected->getId() );

		$this->assertEquals( $expected, $item );
		$this->assertEquals( '32.00', $item->getPrice()->getCosts() );
		$this->assertEquals( '5.00', $item->getPrice()->getRebate() );
		$this->assertEquals( '112.4034', $item->getPrice()->getTaxValue() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$orderProductManager = \Aimeos\MShop\Order\Manager\Factory::create( $this->context )
			->getSubManager( 'base' )->getSubManager( 'product' );

		$search = $this->object->filter()->add( ['order.base.costs' => '1.50', 'order.base.editor' => $this->editor] );
		$item = $this->object->search( $search, ['order/base/product'] )
			->first( new \RuntimeException( 'No order base item found' ) );


		$item->setId( null );
		$item->setComment( 'Unittest1' );
		$resultSaved = $this->object->save( $item );

		$product = $item->getProducts()->first()->setBaseId( $item->getId() )->setId( null );
		$orderProductManager->save( $product );

		$itemSaved = $this->object->get( $item->getId() );
		$itemPrice = $item->getPrice();
		$itemSavedPrice = $item->getPrice();


		$itemExp = clone $itemSaved;
		$itemExp->setComment( 'Unittest2' );
		$itemExp->setCustomerId( 'unittest1' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );
		$itemExpPrice = $itemExp->getPrice();
		$itemUpdPrice = $itemUpd->getPrice();


		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->getLocale()->getLanguageId(), $itemSaved->getLocale()->getLanguageId() );
		$this->assertEquals( $item->getCustomerReference(), $itemSaved->getCustomerReference() );
		$this->assertEquals( $item->getComment(), $itemSaved->getComment() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );
		$this->assertEquals( $itemPrice->getValue(), $itemSavedPrice->getValue() );
		$this->assertEquals( $itemPrice->getCosts(), $itemSavedPrice->getCosts() );
		$this->assertEquals( $itemPrice->getRebate(), $itemSavedPrice->getRebate() );
		$this->assertEquals( $itemPrice->getTaxValue(), $itemSavedPrice->getTaxValue() );
		$this->assertEquals( $itemPrice->getCurrencyId(), $itemSavedPrice->getCurrencyId() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->getLocale()->getLanguageId(), $itemUpd->getLocale()->getLanguageId() );
		$this->assertEquals( $itemExp->getCustomerReference(), $itemUpd->getCustomerReference() );
		$this->assertEquals( $itemExp->getComment(), $itemUpd->getComment() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );
		$this->assertEquals( $itemExpPrice->getValue(), $itemUpdPrice->getValue() );
		$this->assertEquals( $itemExpPrice->getCosts(), $itemUpdPrice->getCosts() );
		$this->assertEquals( $itemExpPrice->getRebate(), $itemUpdPrice->getRebate() );
		$this->assertEquals( $itemExpPrice->getTaxValue(), $itemUpdPrice->getTaxValue() );
		$this->assertEquals( $itemExpPrice->getCurrencyId(), $itemUpdPrice->getCurrencyId() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testCreateSearchDefault()
	{
		$search = $this->object->filter( true );

		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $search );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $search->getConditions() );

		$list = $search->getConditions()->getExpressions();
		$this->assertArrayHasKey( 0, $list );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Compare\Iface::class, $list[0] );
		$this->assertEquals( 'order.base.customerid', $list[0]->getName() );
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

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.base.id', null );
		$expr[] = $search->compare( '==', 'order.base.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '>=', 'order.base.customerid', '' );
		$expr[] = $search->compare( '==', 'order.base.languageid', 'de' );
		$expr[] = $search->compare( '==', 'order.base.currencyid', 'EUR' );
		$expr[] = $search->compare( '==', 'order.base.price', '53.50' );
		$expr[] = $search->compare( '==', 'order.base.costs', '1.50' );
		$expr[] = $search->compare( '==', 'order.base.rebate', '14.50' );
		$expr[] = $search->compare( '==', 'order.base.taxvalue', '0.0000' );
		$expr[] = $search->compare( '~=', 'order.base.customerref', 'ABC-1234' );
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
		$expr[] = $search->compare( '==', 'order.base.product.orderproductid', null );
		$expr[] = $search->compare( '>=', 'order.base.product.type', '' );
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
		$expr[] = $search->compare( '==', 'order.base.product.taxvalue', '0.0000' );
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
		$expr[] = $search->compare( '!=', 'order.base.service.serviceid', null );
		$expr[] = $search->compare( '==', 'order.base.service.code', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.name', 'unitpaymentcode' );
		$expr[] = $search->compare( '==', 'order.base.service.price', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.service.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.base.service.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.base.service.taxvalue', '0.0000' );
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
		$ref = ['order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];
		$result = $this->object->search( $search, $ref, $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, $result->count() );

		$item = $result->first();
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $item );
		$this->assertEquals( 2, count( $item->getAddresses() ) );
		$this->assertEquals( 2, count( $item->getCoupons() ) );
		$this->assertEquals( 4, count( $item->getProducts() ) );
		$this->assertEquals( 2, count( $item->getServices() ) );
	}


	public function testSearchItemsRef()
	{
		$search = $this->object->filter()->slice( 0, 1 );

		$search->setConditions( $search->compare( '!=', 'order.base.customerid', '' ) );
		$result = $this->object->search( $search, ['customer'] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNotNull( $result->first()->getCustomerItem() );
	}


	public function testSearchItemsTotal()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '>=', 'order.base.customerid', '' ),
			$search->compare( '==', 'order.base.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$total = 0;
		$items = $this->object->search( $search, [], $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertGreaterThanOrEqual( 4, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsDefault()
	{
		$search = $this->object->filter( true );
		$items = $this->object->search( $search );

		$this->assertEquals( 0, count( $items ) );
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

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'address', 'unknown' );
	}


	public function testLoad()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId() );


		foreach( $order->getAddresses() as $addresses )
		{
			foreach( $addresses as $address )
			{
				$this->assertNotEquals( '', $address->getId() );
				$this->assertNotEquals( '', $address->getId() );
				$this->assertNotEquals( '', $address->getBaseId() );
			}
		}

		$this->assertEquals( 2, count( $order->getCoupons() ) );

		foreach( $order->getCoupons() as $code => $products )
		{
			$this->assertNotEquals( '', $code );

			foreach( $products as $product ) {
				$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $product );
			}
		}

		foreach( $order->getProducts() as $product )
		{
			$this->assertNotEquals( '', $product->getId() );
			$this->assertNotEquals( '', $product->getId() );
			$this->assertNotEquals( '', $product->getBaseId() );
			$this->assertGreaterThan( 0, $product->getPosition() );
		}

		foreach( $order->getServices() as $list )
		{
			foreach( $list as $service )
			{
				$this->assertNotEquals( '', $service->getId() );
				$this->assertNotEquals( '', $service->getId() );
				$this->assertNotEquals( '', $service->getBaseId() );
			}
		}
	}


	public function testLoadNone()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_NONE );

		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
	}


	public function testLoadAddress()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS );

		$this->assertGreaterThan( 0, count( $order->getAddresses() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
	}


	public function testLoadProduct()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
	}


	public function testLoadCoupon()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertGreaterThan( 0, count( $order->getCoupons() ) );
		$this->assertEquals( [], $order->getServices()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
	}


	public function testLoadService()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );

		$this->assertGreaterThan( 0, count( $order->getServices() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
	}


	public function testLoadFresh()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );


		$this->assertEquals( 2, count( $order->getCoupons() ) );

		foreach( $order->getAddresses() as $list )
		{
			foreach( $list as $address )
			{
				$this->assertEquals( null, $address->getId() );
				$this->assertEquals( null, $address->getBaseId() );
			}
		}

		foreach( $order->getProducts() as $product )
		{
			$this->assertEquals( null, $product->getId() );
			$this->assertEquals( null, $product->getBaseId() );
			$this->assertEquals( null, $product->getPosition() );
		}

		foreach( $order->getServices() as $list )
		{
			foreach( $list as $service )
			{
				$this->assertEquals( null, $service->getId() );
				$this->assertEquals( null, $service->getBaseId() );
			}
		}
	}


	public function testLoadFreshNone()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_NONE, true );

		$this->assertEquals( [], $order->getAddresses()->toArray() );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
	}


	public function testLoadFreshAddress()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS, true );

		$this->assertGreaterThan( 0, count( $order->getAddresses() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
	}


	public function testLoadFreshProduct()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT, true );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
	}


	public function testLoadFreshCoupon()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON, true );

		$this->assertEquals( [], $order->getAddresses()->toArray() );
		$this->assertEquals( 2, count( $order->getCoupons() ) );
		$this->assertEquals( [], $order->getProducts()->toArray() );
		$this->assertEquals( [], $order->getServices()->toArray() );
	}


	public function testLoadFreshService()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE, true );

		$this->assertGreaterThan( 0, count( $order->getServices() ) );
		$this->assertEquals( [], $order->getCoupons()->toArray() );
		$this->assertEquals( [], $order->getAddresses()->toArray() );
		$this->assertEquals( [], $order->getProducts()->toArray() );
	}


	public function testStore()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId );
		$this->object->delete( $newBasketId );


		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		$this->assertEquals( 1.50, $basket->getPrice()->getCosts() );

		$pos = 0;
		$products = $basket->getProducts();
		$this->assertEquals( 2, count( $products ) );

		foreach( $products as $product )
		{
			$this->assertGreaterThanOrEqual( 2, count( $product->getAttributeItems() ) );
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


	public function testStoreExisting()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );
		$newBasketId = $basket->getId();
		$this->object->store( $basket );
		$newBasket = $this->object->load( $newBasketId );

		$this->object->delete( $newBasketId );


		foreach( $basket->getAddresses() as $key => $list )
		{
			foreach( $list as $pos => $address ) {
				$this->assertEquals( $address->getId(), $newBasket->getAddress( $key, $pos )->getId() );
			}
		}

		$newProducts = $newBasket->getProducts();

		foreach( $basket->getProducts() as $key => $product ) {
			$this->assertEquals( $product->getId(), $newProducts[$key]->getId() );
		}

		$newServices = $newBasket->getServices();

		foreach( $basket->getServices() as $key => $list )
		{
			foreach( $list as $pos => $service ) {
				$this->assertEquals( $service->getId(), $newServices[$key][$pos]->getId() );
			}
		}
	}


	public function testStoreBundles()
	{
		$search = $this->object->filter()->add( ['order.base.sitecode' => 'unittest', 'order.base.price' => 2400.00] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId );
		$this->object->delete( $newBasketId );

		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		$pos = 0;
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


	public function testStoreNone()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Item\Base\Base::PARTS_NONE );
		$this->object->delete( $basket->getId() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->load( $basket->getId() );
	}


	public function testStoreAddress()
	{
		$item = $this->getOrderItem();

		$parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT | \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS;
		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, $parts );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getAddresses() ) );
		$this->assertGreaterThan( 0, count( $basket->getProducts() ) );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
		$this->assertEquals( [], $basket->getServices()->toArray() );
	}


	public function testStoreProduct()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getProducts() ) );
		$this->assertEquals( [], $basket->getAddresses()->toArray() );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
		$this->assertEquals( [], $basket->getServices()->toArray() );
	}


	public function testStoreService()
	{
		$item = $this->getOrderItem();

		$parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT | \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE;
		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, $parts );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );
		$this->object->delete( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getServices() ) );
		$this->assertGreaterThan( 0, count( $basket->getProducts() ) );
		$this->assertEquals( [], $basket->getAddresses()->toArray() );
		$this->assertEquals( [], $basket->getCoupons()->toArray() );
	}


	public function testLoadStoreCoupons()
	{
		$search = $this->object->filter()->add( ['order.base.price' => '53.50'] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );

		$parts = \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL ^ \Aimeos\MShop\Order\Item\Base\Base::PARTS_COUPON;
		$basket = $this->object->load( $item->getId(), $parts, true );

		$this->assertEquals( '58.50', $basket->getPrice()->getValue() );
		$this->assertEquals( '1.50', $basket->getPrice()->getCosts() );
		$this->assertEquals( 0, count( $basket->getCoupons() ) );

		$productBasket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL, true );

		$basket->addCoupon( 'CDEF' );
		$basket->addCoupon( '90AB' );
		$this->assertEquals( 2, count( $basket->getCoupons() ) );

		$this->object->store( $basket );
		$newBasket = $this->object->load( $basket->getId() );
		$this->object->delete( $newBasket->getId() );

		$this->assertEquals( '52.50', $newBasket->getPrice()->getValue() );
		$this->assertEquals( '1.50', $newBasket->getPrice()->getCosts() );
		$this->assertEquals( '6.00', $newBasket->getPrice()->getRebate() );
		$this->assertEquals( 2, count( $newBasket->getCoupons() ) );
	}


	/**
	 * Returns an order base item
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Iface Order base item
	 * @throws \Exception If no found
	 */
	protected function getOrderItem()
	{
		$search = $this->object->filter()->add( [
			'order.base.sitecode' => 'unittest',
			'order.base.price' => 53.50,
			'order.base.rebate' => 14.50,
			'order.base.editor' => $this->editor
		] );

		return $this->object->search( $search )->first( new \RuntimeException( 'No order found' ) );
	}
}

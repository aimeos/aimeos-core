<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Order\Manager\Base;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $editor = '';


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();

		$this->object = new \Aimeos\MShop\Order\Manager\Base\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.base.rebate' );

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( '5.00', $result );
		$this->assertEquals( 2, $result['5.00'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $this->object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.costs', '1.50' ) );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new \Aimeos\MShop\Order\Exception( 'No order base item found' );
		}

		$this->assertEquals( $expected, $this->object->getItem( $expected->getId() ) );
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
			$search->compare( '==', 'order.base.costs', '1.50' ),
			$search->compare( '==', 'order.base.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order base item found.' );
		}

		$item->setId( null );
		$item->setComment( 'Unittest1' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );
		$itemPrice = $item->getPrice();
		$itemSavedPrice = $item->getPrice();

		$itemExp = clone $itemSaved;
		$itemExp->setComment( 'Unittest2' );
		$itemExp->setCustomerId( 'unittest2' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );
		$itemExpPrice = $itemExp->getPrice();
		$itemUpdPrice = $itemUpd->getPrice();


		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCustomerId(), $itemSaved->getCustomerId() );
		$this->assertEquals( $item->getLocale()->getLanguageId(), $itemSaved->getLocale()->getLanguageId() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );
		$this->assertEquals( $itemPrice->getValue(), $itemSavedPrice->getValue() );
		$this->assertEquals( $itemPrice->getCosts(), $itemSavedPrice->getCosts() );
		$this->assertEquals( $itemPrice->getRebate(), $itemSavedPrice->getRebate() );
		$this->assertEquals( $itemPrice->getCurrencyId(), $itemSavedPrice->getCurrencyId() );
		$this->assertEquals( $item->getProducts(), $itemSaved->getProducts() );
		$this->assertEquals( $item->getAddresses(), $itemSaved->getAddresses() );
		$this->assertEquals( $item->getCoupons(), $itemSaved->getCoupons() );
		$this->assertEquals( $item->getServices(), $itemSaved->getServices() );
		$this->assertEquals( $item->getComment(), $itemSaved->getComment() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getSiteCode(), $itemSaved->getSiteCode() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCustomerId(), $itemUpd->getCustomerId() );
		$this->assertEquals( $itemExp->getLocale()->getLanguageId(), $itemUpd->getLocale()->getLanguageId() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );
		$this->assertEquals( $itemExpPrice->getValue(), $itemUpdPrice->getValue() );
		$this->assertEquals( $itemExpPrice->getCosts(), $itemUpdPrice->getCosts() );
		$this->assertEquals( $itemExpPrice->getRebate(), $itemUpdPrice->getRebate() );
		$this->assertEquals( $itemExpPrice->getCurrencyId(), $itemUpdPrice->getCurrencyId() );
		$this->assertEquals( $itemExp->getProducts(), $itemUpd->getProducts() );
		$this->assertEquals( $itemExp->getAddresses(), $itemUpd->getAddresses() );
		$this->assertEquals( $itemExp->getCoupons(), $itemUpd->getCoupons() );
		$this->assertEquals( $itemExp->getServices(), $itemUpd->getServices() );
		$this->assertEquals( $itemExp->getComment(), $itemUpd->getComment() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getSiteCode(), $itemUpd->getSiteCode() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
	}


	public function testCreateSearchDefault()
	{
		$search = $this->object->createSearch( true );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $search );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Combine\\Iface', $search->getConditions() );

		$list = $search->getConditions()->getExpressions();
		$this->assertArrayHasKey( 0, $list );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\Iface', $list[0] );
		$this->assertEquals( 'order.base.customerid', $list[0]->getName() );
	}


	public function testSearchItems()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

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
		$expr[] = $search->compare( '==', 'order.base.product.orderproductid', null );
		$expr[] = $search->compare( '>=', 'order.base.product.type', '' );
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
		$expr[] = $search->compare( '!=', 'order.base.service.serviceid', null );
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
	}


	public function testSearchItemsTotal()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '>=', 'order.base.customerid', '' ),
			$search->compare( '==', 'order.base.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$total = 0;
		$items = $this->object->searchItems( $search, [], $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 4, $total );

		foreach( $items as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsDefault()
	{
		$search = $this->object->createSearch(  true );
		$items = $this->object->searchItems( $search );

		$this->assertEquals( 0, count( $items ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'address' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'address', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'coupon' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'coupon', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'product' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'product', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'service' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'service', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'address', 'unknown' );
	}


	public function testLoad()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId() );


		foreach( $order->getAddresses() as $address )
		{
			$this->assertInternalType( 'string', $address->getId() );
			$this->assertNotEquals( '', $address->getId() );
			$this->assertInternalType( 'integer', $address->getBaseId() );
		}

		$this->assertEquals( 2, count( $order->getCoupons() ) );

		foreach( $order->getCoupons() as $code => $products )
		{
			$this->assertNotEquals( '', $code );

			foreach( $products as $product ) {
				$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $product );
			}
		}

		foreach( $order->getProducts() as $product )
		{
			$this->assertInternalType( 'string', $product->getId() );
			$this->assertNotEquals( '', $product->getId() );
			$this->assertInternalType( 'integer', $product->getBaseId() );
			$this->assertGreaterThan( 0, $product->getPosition() );
		}

		foreach( $order->getServices() as $service )
		{
			$this->assertInternalType( 'string', $service->getId() );
			$this->assertNotEquals( '', $service->getId() );
			$this->assertInternalType( 'integer', $service->getBaseId() );
		}
	}


	public function testLoadNone()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_NONE );

		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getServices() );
		$this->assertEquals( [], $order->getAddresses() );
	}


	public function testLoadAddress()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS );

		$this->assertGreaterThan( 0, count( $order->getAddresses() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getServices() );
	}


	public function testLoadProduct()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getServices() );
		$this->assertEquals( [], $order->getAddresses() );
	}


	public function testLoadCoupon()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertGreaterThan( 0, count( $order->getCoupons() ) );
		$this->assertEquals( [], $order->getServices() );
		$this->assertEquals( [], $order->getAddresses() );
	}


	public function testLoadService()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE );

		$this->assertGreaterThan( 0, count( $order->getServices() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getAddresses() );
	}


	public function testLoadFresh()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );


		$this->assertEquals( 0, count( $order->getCoupons() ) );

		foreach( $order->getAddresses() as $address )
		{
			$this->assertEquals( null, $address->getId() );
			$this->assertEquals( null, $address->getBaseId() );
		}

		foreach( $order->getProducts() as $product )
		{
			$this->assertEquals( null, $product->getId() );
			$this->assertEquals( null, $product->getBaseId() );
			$this->assertEquals( null, $product->getPosition() );
		}

		foreach( $order->getServices() as $service )
		{
			$this->assertEquals( null, $service->getId() );
			$this->assertEquals( null, $service->getBaseId() );
		}
	}


	public function testLoadFreshNone()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_NONE, true );

		$this->assertEquals( [], $order->getAddresses() );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getServices() );
	}


	public function testLoadFreshAddress()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS, true );

		$this->assertGreaterThan( 0, count( $order->getAddresses() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getServices() );
	}


	public function testLoadFreshProduct()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT, true );

		$this->assertGreaterThan( 0, count( $order->getProducts() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getAddresses() );
		$this->assertEquals( [], $order->getServices() );
	}


	public function testLoadFreshCoupon()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_COUPON, true );

		$this->assertEquals( [], $order->getAddresses() );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getProducts() );
		$this->assertEquals( [], $order->getServices() );
	}


	public function testLoadFreshService()
	{
		$item = $this->getOrderItem();
		$order = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE, true );

		$this->assertGreaterThan( 0, count( $order->getServices() ) );
		$this->assertEquals( [], $order->getCoupons() );
		$this->assertEquals( [], $order->getAddresses() );
		$this->assertEquals( [], $order->getProducts() );
	}


	public function testStore()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId );
		$this->object->deleteItem( $newBasketId );


		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		$this->assertEquals( 6.50, $basket->getPrice()->getCosts() );

		$pos = 1;
		$products = $basket->getProducts();
		$this->assertEquals( 4, count( $products ) );

		foreach( $products as $product )
		{
			$this->assertEquals( 2, count( $product->getAttributes() ) );
			$this->assertEquals( $pos++, $product->getPosition() );
		}

		$this->assertEquals( 2, count( $basket->getAddresses() ) );

		$services = $basket->getServices();
		$this->assertEquals( 2, count( $services ) );

		$attributes = [];
		foreach( $services as $service ) {
			$attributes[$service->getCode()] = $service->getAttributes();
		}

		$this->assertEquals( 9, count( $attributes['OGONE'] ) );
		$this->assertEquals( 0, count( $attributes['73'] ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $newBasketId );
	}


	public function testStoreExisting()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );
		$newBasketId = $basket->getId();
		$this->object->store( $basket );
		$newBasket = $this->object->load( $newBasketId );

		$this->object->deleteItem( $newBasketId );


		$newAddresses = $newBasket->getAddresses();

		foreach( $basket->getAddresses() as $key => $address ) {
			$this->assertEquals( $address->getId(), $newAddresses[$key]->getId() );
		}

		$newProducts = $newBasket->getProducts();

		foreach( $basket->getProducts() as $key => $product )
		{
			// key+1 is because of the array_splice() in \Aimeos\MShop\Order\Item\Base\Standard::addProduct()
			// so it doesn't make sense to hand over the key as second parameter to addProduct() in
			// \Aimeos\MShop\Order\Manager\Base\Standard::loadFresh() to try to enforce a 1-based numbering
			$this->assertEquals( $product->getId(), $newProducts[$key + 1]->getId() );
			$this->assertEquals( $product->getPosition(), $newProducts[$key + 1]->getPosition() );
		}

		$newServices = $newBasket->getServices();

		foreach( $basket->getServices() as $key => $service ) {
			$this->assertEquals( $service->getId(), $newServices[$key]->getId() );
		}
	}


	public function testStoreBundles()
	{
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '==', 'order.base.price', 4800.00 );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) == false ) {
			throw new \RuntimeException( 'No order found' );
		}

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId );
		$this->object->deleteItem( $newBasketId );

		$this->assertEquals( $item->getCustomerId(), $basket->getCustomerId() );
		$this->assertEquals( $basket->getLocale()->getSiteId(), $basket->getSiteId() );

		$pos = 1;
		$products = $basket->getProducts();

		$this->assertEquals( 2, count( $products ) );
		foreach( $products as $product )
		{
			$this->assertEquals( 2, count( $product->getProducts() ) );
			$this->assertEquals( $pos, $product->getPosition() );
			$pos += 3; // two sub-products in between
		}

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $newBasketId );
	}


	public function testStoreNone()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_NONE );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );
		$this->object->deleteItem( $newBasketId );

		$this->assertEquals( [], $basket->getCoupons() );
		$this->assertEquals( [], $basket->getAddresses() );
		$this->assertEquals( [], $basket->getProducts() );
		$this->assertEquals( [], $basket->getServices() );
	}


	public function testStoreAddress()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ADDRESS );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );
		$this->object->deleteItem( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getAddresses() ) );
		$this->assertEquals( [], $basket->getCoupons() );
		$this->assertEquals( [], $basket->getProducts() );
		$this->assertEquals( [], $basket->getServices() );
	}


	public function testStoreProduct()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_PRODUCT );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );
		$this->object->deleteItem( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getProducts() ) );
		$this->assertEquals( [], $basket->getAddresses() );
		$this->assertEquals( [], $basket->getCoupons() );
		$this->assertEquals( [], $basket->getServices() );
	}


	public function testStoreService()
	{
		$item = $this->getOrderItem();

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );
		$this->object->store( $basket, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_SERVICE );

		$newBasketId = $basket->getId();

		$basket = $this->object->load( $newBasketId, \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL );
		$this->object->deleteItem( $newBasketId );

		$this->assertGreaterThan( 0, count( $basket->getServices() ) );
		$this->assertEquals( [], $basket->getAddresses() );
		$this->assertEquals( [], $basket->getCoupons() );
		$this->assertEquals( [], $basket->getProducts() );
	}


	public function testLoadStoreCoupons()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', '672.00' ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order found' );
		}

		$basket = $this->object->load( $item->getId(), \Aimeos\MShop\Order\Manager\Base\Base::PARTS_ALL, true );

		$this->assertEquals( '672.00', $basket->getPrice()->getValue() );
		$this->assertEquals( '37.00', $basket->getPrice()->getCosts() );
		$this->assertEquals( 0, count( $basket->getCoupons() ) );

		$basket->addCoupon( 'CDEF' );
		$basket->addCoupon( '5678', $basket->getProducts() );
		$this->assertEquals( 2, count( $basket->getCoupons() ) );

		$this->object->store( $basket );
		$newBasket = $this->object->load( $basket->getId() );
		$this->object->deleteItem( $newBasket->getId() );

		$this->assertEquals( '1344.00', $newBasket->getPrice()->getValue() );
		$this->assertEquals( '64.00', $newBasket->getPrice()->getCosts() );
		$this->assertEquals( '5.00', $newBasket->getPrice()->getRebate() );
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
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '==', 'order.base.rebate', 14.50 );
		$expr[] = $search->compare( '==', 'order.base.sitecode', 'unittest' );
		$expr[] = $search->compare( '==', 'order.base.price', 53.50 );
		$expr[] = $search->compare( '==', 'order.base.editor', $this->editor );
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order found' );
		}

		return $item;
	}
}

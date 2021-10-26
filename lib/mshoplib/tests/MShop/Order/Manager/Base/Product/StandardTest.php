<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Manager\Base\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Product\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.product.stocktype' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( 5, $result['default'] );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.product.type', 'order.base.product.price', 'avg' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( '253.83', round( $result['default'], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.product.type', 'order.base.product.quantity', 'sum' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( 25, $result['default'] );
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

		$this->assertContains( 'order/base/product', $result );
		$this->assertContains( 'order/base/product/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes( true ) as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Iface::class, $this->object->create() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter( true ) );
	}


	public function testCreateSearchSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.base.product.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.product.orderproductid', null );
		$expr[] = $search->compare( '==', 'order.base.product.orderaddressid', null );
		$expr[] = $search->compare( '>=', 'order.base.product.type', '' );
		$expr[] = $search->compare( '!=', 'order.base.product.productid', null );
		$expr[] = $search->compare( '==', 'order.base.product.parentproductid', '' );
		$expr[] = $search->compare( '==', 'order.base.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.base.product.supplierid', 'unitSupplier001' );
		$expr[] = $search->compare( '==', 'order.base.product.suppliername', 'Test supplier' );
		$expr[] = $search->compare( '==', 'order.base.product.stocktype', 'default' );
		$expr[] = $search->compare( '==', 'order.base.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.base.product.description', '' );
		$expr[] = $search->compare( '==', 'order.base.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '>=', 'order.base.product.timeframe', '4-5d' );
		$expr[] = $search->compare( '>=', 'order.base.product.target', '' );
		$expr[] = $search->compare( '==', 'order.base.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.base.product.qtyopen', 6 );
		$expr[] = $search->compare( '==', 'order.base.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.base.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.base.product.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.base.product.taxflag', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.taxvalue', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.base.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.statuspayment', 5 );
		$expr[] = $search->compare( '==', 'order.base.product.statusdelivery', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.notes', 'test note' );
		$expr[] = $search->compare( '!=', 'order.base.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.base.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.quantity', 1 );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemRef()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'order.base.product.prodcode', 'CNE' ) );
		$result = $this->object->search( $search, ['product'] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNotNull( $result->first()->getProductItem() );
	}


	public function testSearchItemTotal()
	{
		$total = 0;
		$search = $this->object->filter();

		$conditions = array(
			$search->compare( '==', 'order.base.product.supplierid', 'unitSupplier001' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 2 );

		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 14, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.base.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'empty result' );
		}

		$this->assertEquals( $item, $this->object->get( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.base.product.price', '0.00' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$orderItems = $this->object->search( $search )->toArray();

		if( !( $item = reset( $orderItems ) ) ) {
			throw new \RuntimeException( 'empty search result' );
		}

		$item->setId( null );
		$item->setPosition( $item->getPosition() + 1 );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProductCode( 'unitUpdCode' );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemUpd->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );
		$this->assertEquals( $item->getOrderAddressId(), $itemSaved->getOrderAddressId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getProductCode(), $itemSaved->getProductCode() );
		$this->assertEquals( $item->getSupplierId(), $itemSaved->getSupplierId() );
		$this->assertEquals( $item->getSupplierName(), $itemSaved->getSupplierName() );
		$this->assertEquals( $item->getStockType(), $itemSaved->getStockType() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getDescription(), $itemSaved->getDescription() );
		$this->assertEquals( $item->getMediaUrl(), $itemSaved->getMediaUrl() );
		$this->assertEquals( $item->getTarget(), $itemSaved->getTarget() );
		$this->assertEquals( $item->getPrice()->getValue(), $itemSaved->getPrice()->getValue() );
		$this->assertEquals( $item->getPrice()->getCosts(), $itemSaved->getPrice()->getCosts() );
		$this->assertEquals( $item->getPrice()->getRebate(), $itemSaved->getPrice()->getRebate() );
		$this->assertEquals( $item->getPrice()->getTaxflag(), $itemSaved->getPrice()->getTaxflag() );
		$this->assertEquals( $item->getPrice()->getTaxValue(), $itemSaved->getPrice()->getTaxValue() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );
		$this->assertEquals( $item->getQuantityOpen(), $itemSaved->getQuantityOpen() );
		$this->assertEquals( $item->getStatusPayment(), $itemSaved->getStatusPayment() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getFlags(), $itemSaved->getFlags() );
		$this->assertEquals( $item->getNotes(), $itemSaved->getNotes() );
		$this->assertNotEquals( [], $item->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );


		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );
		$this->assertEquals( $itemExp->getOrderAddressId(), $itemUpd->getOrderAddressId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getProductCode(), $itemUpd->getProductCode() );
		$this->assertEquals( $itemExp->getSupplierId(), $itemUpd->getSupplierId() );
		$this->assertEquals( $itemExp->getSupplierName(), $itemUpd->getSupplierName() );
		$this->assertEquals( $itemExp->getStockType(), $itemUpd->getStockType() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getDescription(), $itemUpd->getDescription() );
		$this->assertEquals( $itemExp->getMediaUrl(), $itemUpd->getMediaUrl() );
		$this->assertEquals( $itemExp->getTarget(), $itemUpd->getTarget() );
		$this->assertEquals( $itemExp->getPrice()->getValue(), $itemUpd->getPrice()->getValue() );
		$this->assertEquals( $itemExp->getPrice()->getCosts(), $itemUpd->getPrice()->getCosts() );
		$this->assertEquals( $itemExp->getPrice()->getRebate(), $itemUpd->getPrice()->getRebate() );
		$this->assertEquals( $itemExp->getPrice()->getTaxflag(), $itemUpd->getPrice()->getTaxflag() );
		$this->assertEquals( $itemExp->getPrice()->getTaxValue(), $itemUpd->getPrice()->getTaxValue() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );
		$this->assertEquals( $itemExp->getQuantityOpen(), $itemUpd->getQuantityOpen() );
		$this->assertEquals( $itemExp->getStatusPayment(), $itemUpd->getStatusPayment() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getFlags(), $itemUpd->getFlags() );
		$this->assertEquals( $itemExp->getNotes(), $itemUpd->getNotes() );
		$this->assertNotEquals( [], $itemUpd->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
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


	public function testGetSubManagerInvalidTypeName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( '$$$' );
	}


	public function testGetSubManagerInvalidDefaultName()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'attribute', '$$$' );
	}
}

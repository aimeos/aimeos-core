<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Manager\Product;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelper::context()->editor();
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Order\Manager\Product\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.product.editor', 'core' ) );
		$result = $this->object->aggregate( $search, 'order.product.stocktype' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( 5, $result['default'] );
	}


	public function testAggregateAvg()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.product.editor', 'core' ) );
		$result = $this->object->aggregate( $search, 'order.product.type', 'order.product.price', 'avg' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( '253.83', round( $result['default'], 2 ) );
	}


	public function testAggregateSum()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.product.editor', 'core' ) );
		$result = $this->object->aggregate( $search, 'order.product.type', 'order.product.quantity', 'sum' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( 25, $result['default'] );
	}


	public function testAggregateTotal()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.product.editor', 'core' ) );
		$result = $this->object->aggregate( $search, 'order.product.type', 'agg:order.product:total()', 'sum' )->toArray();

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'default', $result );
		$this->assertEquals( 3139, $result['default'] );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Iface::class, $this->object->create() );
	}


	public function testCreateAttributeItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Product\Attribute\Iface::class, $this->object->createAttributeItem() );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'order/product', $result );
		$this->assertContains( 'order/product/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes( true ) as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter( true ) );
	}


	public function testCreateSearchSite()
	{
		$result = $this->object->filter( false, true );
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Expression\Combine\Iface::class, $result->getConditions() );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->locale()->getSiteId();

		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.product.id', null );
		$expr[] = $search->compare( '==', 'order.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.product.parentid', null );
		$expr[] = $search->compare( '==', 'order.product.orderproductid', null );
		$expr[] = $search->compare( '==', 'order.product.orderaddressid', null );
		$expr[] = $search->compare( '>=', 'order.product.type', '' );
		$expr[] = $search->compare( '!=', 'order.product.productid', null );
		$expr[] = $search->compare( '==', 'order.product.parentproductid', '' );
		$expr[] = $search->compare( '==', 'order.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.product.vendor', 'Test vendor' );
		$expr[] = $search->compare( '==', 'order.product.stocktype', 'default' );
		$expr[] = $search->compare( '==', 'order.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.product.description', '' );
		$expr[] = $search->compare( '==', 'order.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '>=', 'order.product.timeframe', '4-5d' );
		$expr[] = $search->compare( '>=', 'order.product.target', '' );
		$expr[] = $search->compare( '==', 'order.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.product.qtyopen', 6 );
		$expr[] = $search->compare( '==', 'order.product.scale', 0.1 );
		$expr[] = $search->compare( '==', 'order.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.product.rebate', '0.00' );
		$expr[] = $search->compare( '=~', 'order.product.taxrates', '{' );
		$expr[] = $search->compare( '==', 'order.product.taxflag', 1 );
		$expr[] = $search->compare( '==', 'order.product.taxvalue', '0.00' );
		$expr[] = $search->compare( '==', 'order.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.product.statuspayment', 5 );
		$expr[] = $search->compare( '==', 'order.product.statusdelivery', 1 );
		$expr[] = $search->compare( '==', 'order.product.notes', 'test note' );
		$expr[] = $search->compare( '!=', 'order.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.product.attribute.name', '33' );
		$expr[] = $search->compare( '==', 'order.product.attribute.quantity', 1 );
		$expr[] = $search->compare( '!=', 'order.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.product.attribute.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemRef()
	{
		$search = $this->object->filter()->add( ['order.product.prodcode' => 'CNE'] )->slice( 0, 1 );
		$result = $this->object->search( $search, ['product'] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNotNull( $result->first()->getProductItem() );
	}


	public function testSearchItemTotal()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 2 );
		$results = $this->object->search( $search, [], $total );

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
			$search->compare( '==', 'order.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.product.editor', $this->editor )
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
		$search = $this->object->filter()->add( ['order.product.price' => '0.00'] );
		$item = $this->object->search( $search )->first( new \RuntimeException( 'empty search result' ) );

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
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );
		$this->assertEquals( $item->getOrderAddressId(), $itemSaved->getOrderAddressId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getProductCode(), $itemSaved->getProductCode() );
		$this->assertEquals( $item->getVendor(), $itemSaved->getVendor() );
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
		$this->assertEquals( $item->getScale(), $itemSaved->getScale() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );
		$this->assertEquals( $item->getQuantityOpen(), $itemSaved->getQuantityOpen() );
		$this->assertEquals( $item->getStatusPayment(), $itemSaved->getStatusPayment() );
		$this->assertEquals( $item->getStatusDelivery(), $itemSaved->getStatusDelivery() );
		$this->assertEquals( $item->getFlags(), $itemSaved->getFlags() );
		$this->assertEquals( $item->getNotes(), $itemSaved->getNotes() );
		$this->assertNotEquals( [], $item->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemSaved->editor() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );


		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );
		$this->assertEquals( $itemExp->getOrderAddressId(), $itemUpd->getOrderAddressId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getProductCode(), $itemUpd->getProductCode() );
		$this->assertEquals( $itemExp->getVendor(), $itemUpd->getVendor() );
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
		$this->assertEquals( $itemExp->getScale(), $itemUpd->getScale() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );
		$this->assertEquals( $itemExp->getQuantityOpen(), $itemUpd->getQuantityOpen() );
		$this->assertEquals( $itemExp->getStatusPayment(), $itemUpd->getStatusPayment() );
		$this->assertEquals( $itemExp->getStatusDelivery(), $itemUpd->getStatusDelivery() );
		$this->assertEquals( $itemExp->getFlags(), $itemUpd->getFlags() );
		$this->assertEquals( $itemExp->getNotes(), $itemUpd->getNotes() );
		$this->assertNotEquals( [], $itemUpd->getAttributeItems()->toArray() );

		$this->assertEquals( $this->editor, $itemUpd->editor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertMatchesRegularExpression( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'attribute' ) );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->getSubManager( 'attribute', 'Standard' ) );

		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'attribute', 'unknown' );
	}


	public function testGetSubManagerInvalidTypeName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( '%^unknown' );
	}


	public function testGetSubManagerInvalidDefaultName()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'attribute', '%^unknown' );
	}
}

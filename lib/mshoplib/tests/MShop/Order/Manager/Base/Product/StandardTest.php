<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Order\Manager\Base\Product;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Product\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.product.editor', 'core:unittest' ) );
		$result = $this->object->aggregate( $search, 'order.base.stocktype' );

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'unit_type1', $result );
		$this->assertEquals( 11, $result['unit_type1'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
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
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Product\\Iface', $this->object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch( true ) );
	}


	public function testSearchItem()
	{
		$siteid = $this->context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'order.base.product.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.baseid', null );
		$expr[] = $search->compare( '==', 'order.base.product.orderproductid', null );
		$expr[] = $search->compare( '>=', 'order.base.product.type', '' );
		$expr[] = $search->compare( '!=', 'order.base.product.productid', null );
		$expr[] = $search->compare( '==', 'order.base.product.prodcode', 'CNE' );
		$expr[] = $search->compare( '==', 'order.base.product.suppliercode', 'unitsupplier' );
		$expr[] = $search->compare( '==', 'order.base.stocktype', 'unit_type1' );
		$expr[] = $search->compare( '==', 'order.base.product.name', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'order.base.product.mediaurl', 'somewhere/thump1.jpg' );
		$expr[] = $search->compare( '==', 'order.base.product.quantity', 9 );
		$expr[] = $search->compare( '==', 'order.base.product.price', '4.50' );
		$expr[] = $search->compare( '==', 'order.base.product.costs', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.rebate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.taxrate', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.taxflag', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.taxvalue', '0.00' );
		$expr[] = $search->compare( '==', 'order.base.product.flags', 0 );
		$expr[] = $search->compare( '==', 'order.base.product.position', 1 );
		$expr[] = $search->compare( '==', 'order.base.product.status', 1 );
		$expr[] = $search->compare( '!=', 'order.base.product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.base.product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.parentid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		$conditions = array(
			$search->compare( '==', 'order.base.product.suppliercode', 'unitsupplier' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 2 );

		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 14, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.base.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'empty result' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
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
			$search->compare( '==', 'order.base.product.price', '0.00' ),
			$search->compare( '==', 'order.base.product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$orderItems = $this->object->searchItems( $search );

		if( !( $item = reset( $orderItems ) ) ) {
			throw new \RuntimeException( 'empty search result' );
		}

		$item->setId( null );
		$item->setPosition( $item->getPosition() + 1 );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProductCode( 'unitUpdCode' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemUpd->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertNotEquals( [], $item->getAttributes() );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId() );
		$this->assertEquals( $item->getOrderProductId(), $itemSaved->getOrderProductId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getProductCode(), $itemSaved->getProductCode() );
		$this->assertEquals( $item->getSupplierCode(), $itemSaved->getSupplierCode() );
		$this->assertEquals( $item->getStockType(), $itemSaved->getStockType() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getMediaUrl(), $itemSaved->getMediaUrl() );
		$this->assertEquals( $item->getPrice(), $itemSaved->getPrice() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getFlags(), $itemSaved->getFlags() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );


		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId() );
		$this->assertEquals( $itemExp->getOrderProductId(), $itemUpd->getOrderProductId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getProductCode(), $itemUpd->getProductCode() );
		$this->assertEquals( $itemExp->getSupplierCode(), $itemUpd->getSupplierCode() );
		$this->assertEquals( $itemExp->getStockType(), $itemUpd->getStockType() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getMediaUrl(), $itemUpd->getMediaUrl() );
		$this->assertEquals( $itemExp->getPrice(), $itemUpd->getPrice() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getFlags(), $itemUpd->getFlags() );
		$this->assertEquals( [], $itemUpd->getAttributes() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'attribute' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'attribute', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'attribute', 'unknown' );
	}


	public function testGetSubManagerInvalidTypeName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( '$$$' );
	}


	public function testGetSubManagerInvalidDefaultName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'attribute', '$$$' );
	}
}

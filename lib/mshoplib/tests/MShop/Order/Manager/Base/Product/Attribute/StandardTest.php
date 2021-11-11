<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Manager\Base\Product\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Product\Attribute\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAggregate()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'order.base.product.attribute.editor', 'core:lib/mshoplib' ) );
		$result = $this->object->aggregate( $search, 'order.base.product.attribute.code' )->toArray();

		$this->assertEquals( 5, count( $result ) );
		$this->assertArrayHasKey( 'width', $result );
		$this->assertEquals( 4, $result['width'] );
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

		$this->assertContains( 'order/base/product/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testCreateItem()
	{
		$actual = $this->object->create();
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Product\Attribute\Iface::class, $actual );
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
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.id', null );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.parentid', null );
		$expr[] = $search->compare( '!=', 'order.base.product.attribute.attributeid', null );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.siteid', $siteid );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.type', 'default' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.code', 'width' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.value', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.name', '33' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.quantity', 1 );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.product.attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.base.product.attribute.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		$conditions = array(
			$search->compare( '==', 'order.base.product.attribute.code', array( 'length', 'width' ) ),
			$search->compare( '==', 'order.base.product.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 8, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'order.base.product.attribute.code', 'size' ),
			$search->compare( '==', 'order.base.product.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$results = $this->object->search( $search )->toArray();

		if( !( $item = reset( $results ) ) ) {
			throw new \RuntimeException( 'empty results' );
		}

		$actual = $this->object->get( $item->getId() );
		$this->assertEquals( $item->getId(), $actual->getId() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$conditions = array(
			$search->compare( '==', 'order.base.product.attribute.value', 33 ),
			$search->compare( '==', 'order.base.product.attribute.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$orderItems = $this->object->search( $search )->toArray();

		if( !( $item = reset( $orderItems ) ) ) {
			throw new \RuntimeException( 'empty search result' );
		}

		$item->setId( null );
		$item->setCode( 'unittest1' );
		$item->setName( '33' );
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
		$this->assertEquals( $item->getAttributeId(), $itemSaved->getAttributeId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getName(), $itemSaved->getName() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );
		$this->assertEquals( $item->getQuantity(), $itemSaved->getQuantity() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getAttributeId(), $itemUpd->getAttributeId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getName(), $itemUpd->getName() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );
		$this->assertEquals( $itemExp->getQuantity(), $itemUpd->getQuantity() );

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
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}

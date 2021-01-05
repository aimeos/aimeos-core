<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Stock\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor = '';


	protected function setUp() : void
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Stock\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $this->object->create() );
	}


	public function testCreateItemType()
	{
		$item = $this->object->create( ['stock.type' => 'default'] );
		$this->assertEquals( 'default', $item->getType() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'stock.editor', $this->editor ) );
		$search->slice( 0, 1 );
		$items = $this->object->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setProductId( '-1' );
		$resultSaved = $this->object->save( $item );
		$itemSaved = $this->object->get( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setStockLevel( 50 );
		$resultUpd = $this->object->save( $itemExp );
		$itemUpd = $this->object->get( $itemExp->getId() );

		$this->object->delete( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getStockLevel(), $itemSaved->getStockLevel() );
		$this->assertEquals( $item->getTimeFrame(), $itemSaved->getTimeFrame() );
		$this->assertEquals( $item->getDateBack(), $itemSaved->getDateBack() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getStockLevel(), $itemUpd->getStockLevel() );
		$this->assertEquals( $itemExp->getTimeFrame(), $itemUpd->getTimeFrame() );
		$this->assertEquals( $itemExp->getDateBack(), $itemUpd->getDateBack() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultSaved );
		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $resultUpd );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->get( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->object->filter()->slice( 0, 1 );
		$conditions = array(
			$search->compare( '==', 'stock.stocklevel', 2000 ),
			$search->compare( '==', 'stock.editor', $this->editor )
		);
		$search->setConditions( $search->and( $conditions ) );
		$result = $this->object->search( $search )->toArray();

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No stock item found for level "%1$s".', 2000 ) );
		}

		$actual = $this->object->get( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'stock', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();

		$expr = [];
		$expr[] = $search->compare( '!=', 'stock.id', null );
		$expr[] = $search->compare( '!=', 'stock.siteid', null );
		$expr[] = $search->compare( '!=', 'stock.productid', null );
		$expr[] = $search->compare( '!=', 'stock.type', null );
		$expr[] = $search->compare( '==', 'stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'stock.timeframe', '4-5d' );
		$expr[] = $search->compare( '==', 'stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'stock.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'stock.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'stock.editor', $this->editor );

		$search->setConditions( $search->and( $expr ) );
		$search->slice( 0, 1 );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testDecrease()
	{
		$stockItem = $this->object->create();
		$stockItem->setType( 'unitstock' );
		$stockItem->setProductId( '-1' );
		$stockItem->setStockLevel( 0 );

		$this->object->save( $stockItem );

		$this->object->decrease( ['-1' => 5], 'unitstock' );
		$actual = $this->object->get( $stockItem->getId() );

		$this->object->delete( $stockItem->getId() );

		$this->assertEquals( -5, $actual->getStockLevel() );
	}


	public function testIncrease()
	{
		$stockItem = $this->object->create();
		$stockItem->setType( 'unitstock' );
		$stockItem->setProductId( '-1' );
		$stockItem->setStockLevel( 0 );

		$this->object->save( $stockItem );

		$this->object->increase( ['-1' => 5], 'unitstock' );
		$actual = $this->object->get( $stockItem->getId() );

		$this->object->delete( $stockItem->getId() );

		$this->assertEquals( 5, $actual->getStockLevel() );
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}
}

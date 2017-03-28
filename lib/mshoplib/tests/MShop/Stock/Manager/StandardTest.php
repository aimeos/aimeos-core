<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Stock\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Stock\Manager\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Stock\\Item\\Iface', $this->object->createItem() );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'CNC', [], 'product', 'default' );

		$this->assertEquals( 'CNC', $item->getProductCode() );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Stock\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'stock.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item->setId( null );
		$item->setProductCode( 'XYZ' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setStockLevel( 50 );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getProductCode(), $itemSaved->getProductCode() );
		$this->assertEquals( $item->getStockLevel(), $itemSaved->getStockLevel() );
		$this->assertEquals( $item->getDateBack(), $itemSaved->getDateBack() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getProductCode(), $itemUpd->getProductCode() );
		$this->assertEquals( $itemExp->getStockLevel(), $itemUpd->getStockLevel() );
		$this->assertEquals( $itemExp->getDateBack(), $itemUpd->getDateBack() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'stock.stocklevel', 2000 ),
			$search->compare( '==', 'stock.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No stock item found for level "%1$s".', 2000 ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'stock', $result );
		$this->assertContains( 'stock/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
		$expr[] = $search->compare( '!=', 'stock.id', null );
		$expr[] = $search->compare( '!=', 'stock.siteid', null );
		$expr[] = $search->compare( '!=', 'stock.typeid', null );
		$expr[] = $search->compare( '!=', 'stock.productcode', null );
		$expr[] = $search->compare( '==', 'stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'stock.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'stock.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'stock.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testDecrease()
	{
		$typeManager = $this->object->getSubManager( 'type' );
		$typeItem = $typeManager->findItem( 'unit_type1', [], 'product' );

		$stockItem = $this->object->createItem();
		$stockItem->setTypeId( $typeItem->getId() );
		$stockItem->setProductCode( 'CNC' );
		$stockItem->setStockLevel( 0 );

		$this->object->saveItem( $stockItem );

		$this->object->decrease( 'CNC', $typeItem->getCode(), 5 );
		$actual = $this->object->getItem( $stockItem->getId() );

		$this->object->deleteItem( $stockItem->getId() );

		$this->assertEquals( -5, $actual->getStocklevel() );
	}


	public function testIncrease()
	{
		$typeManager = $this->object->getSubManager( 'type' );
		$typeItem = $typeManager->findItem( 'unit_type1', [], 'product' );

		$stockItem = $this->object->createItem();
		$stockItem->setTypeId( $typeItem->getId() );
		$stockItem->setProductCode( 'CNC' );
		$stockItem->setStockLevel( 0 );

		$this->object->saveItem( $stockItem );

		$this->object->increase( 'CNC', $typeItem->getCode(), 5 );
		$actual = $this->object->getItem( $stockItem->getId() );

		$this->object->deleteItem( $stockItem->getId() );

		$this->assertEquals( 5, $actual->getStocklevel() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}

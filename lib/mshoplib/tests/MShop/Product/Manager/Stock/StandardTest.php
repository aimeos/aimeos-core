<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Product\Manager\Stock;


/**
 * Test class for \Aimeos\MShop\Product\Stock\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = \TestHelper::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Product\Manager\Stock\Standard( \TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Product\\Item\\Stock\\Iface', $this->object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'U:WH' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $productManager->searchItems( $search );

		if( ( $product = reset( $items ) ) === false ) {
			throw new \Exception( 'No product item found' );
		}

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No item found' );
		}

		$item->setId( null );
		$item->setProductId( $product->getId() );
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
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getWarehouseId(), $itemSaved->getWarehouseId() );
		$this->assertEquals( $item->getStockLevel(), $itemSaved->getStockLevel() );
		$this->assertEquals( $item->getDateBack(), $itemSaved->getDateBack() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getWarehouseId(), $itemUpd->getWarehouseId() );
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
			$search->compare( '==', 'product.stock.stocklevel', 2000 ),
			$search->compare( '==', 'product.stock.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \Exception( sprintf( 'No stock item found for level "%1$s".', 2000 ) );
		}

		$actual = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'product/stock', $result );
		$this->assertContains( 'product/stock/warehouse', $result );
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

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.stock.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.siteid', null );
		$expr[] = $search->compare( '!=', 'product.stock.productid', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouseid', null );
		$expr[] = $search->compare( '==', 'product.stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'product.stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.stock.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.stock.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.stock.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testDecrease()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search );

		if( ( $stockItem = reset( $results ) ) === false ) {
			throw new \Exception( 'No stock item found.' );
		}

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$productCode = $productManager->getItem( $stockItem->getProductId() )->getCode();

		$warehouseManager = $this->object->getSubManager( 'warehouse' );
		$warehouseCode = $warehouseManager->getItem( $stockItem->getWarehouseId() )->getCode();

		$this->object->decrease( $productCode, $warehouseCode, 5 );
		$actual = $this->object->getItem( $stockItem->getId() );

		$this->object->saveItem( $stockItem );

		$this->assertEquals( $stockItem->getStocklevel() - 5, $actual->getStocklevel() );
	}


	public function testIncrease()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search );

		if( ( $stockItem = reset( $results ) ) === false ) {
			throw new \Exception( 'No stock item found.' );
		}

		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$productCode = $productManager->getItem( $stockItem->getProductId() )->getCode();

		$warehouseManager = $this->object->getSubManager( 'warehouse' );
		$warehouseCode = $warehouseManager->getItem( $stockItem->getWarehouseId() )->getCode();

		$this->object->increase( $productCode, $warehouseCode, 5 );
		$actual = $this->object->getItem( $stockItem->getId() );

		$this->object->saveItem( $stockItem );

		$this->assertEquals( $stockItem->getStocklevel() + 5, $actual->getStocklevel() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}

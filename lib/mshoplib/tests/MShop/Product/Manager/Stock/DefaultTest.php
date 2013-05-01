<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14682 2012-01-04 11:30:14Z nsendetzky $
 */


/**
 * Test class for MShop_Product_Stock_Default.
 */
class MShop_Product_Manager_Stock_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Product_Manager_Stock_Default( TestHelper::getContext() );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Product_Manager_Stock_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Stock_Interface', $this->_object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'U:WH' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $productManager->searchItems( $search );

		if( ( $product = reset( $items ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->_editor ) );
		$search->setSlice( 0, 1 );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId(null);
		$item->setProductId( $product->getId() );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setStockLevel( 50 );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId() );
		$this->assertEquals( $item->getWarehouseId(), $itemSaved->getWarehouseId() );
		$this->assertEquals( $item->getStockLevel(), $itemSaved->getStockLevel() );
		$this->assertEquals( $item->getDateBack(), $itemSaved->getDateBack() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId() );
		$this->assertEquals( $itemExp->getWarehouseId(), $itemUpd->getWarehouseId() );
		$this->assertEquals( $itemExp->getStockLevel(), $itemUpd->getStockLevel() );
		$this->assertEquals( $itemExp->getDateBack(), $itemUpd->getDateBack() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem($itemSaved->getId());
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.stock.stocklevel', 2000 ),
			$search->compare( '==', 'product.stock.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search );

		if( ($expected = reset($result)) === false ){
			throw new Exception( sprintf( 'No stock item found for level "%1$s".', 2000 ) );
		}

		$actual = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'product.stock.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.siteid', null );
		$expr[] = $search->compare( '!=', 'product.stock.productid', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouseid', null );
		$expr[] = $search->compare( '==', 'product.stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'product.stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.stock.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.stock.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.stock.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr ) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testDecrease()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->_editor ) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search );

		if( ( $stockItem = reset( $results ) ) === false ) {
			throw new Exception( 'No stock item found.' );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$productCode = $productManager->getItem( $stockItem->getProductId() )->getCode();

		$warehouseManager = $this->_object->getSubManager( 'warehouse' );
		$warehouseCode = $warehouseManager->getItem( $stockItem->getWarehouseId() )->getCode();

		$this->_object->decrease( $productCode, $warehouseCode, 5 );
		$actual = $this->_object->getItem( $stockItem->getId() );

		$this->_object->saveItem( $stockItem );

		$this->assertEquals( $stockItem->getStocklevel() - 5, $actual->getStocklevel() );
	}


	public function testIncrease()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.stock.editor', $this->_editor ) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search );

		if( ( $stockItem = reset( $results ) ) === false ) {
			throw new Exception( 'No stock item found.' );
		}

		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$productCode = $productManager->getItem( $stockItem->getProductId() )->getCode();

		$warehouseManager = $this->_object->getSubManager( 'warehouse' );
		$warehouseCode = $warehouseManager->getItem( $stockItem->getWarehouseId() )->getCode();

		$this->_object->increase( $productCode, $warehouseCode, 5 );
		$actual = $this->_object->getItem( $stockItem->getId() );

		$this->_object->saveItem( $stockItem );

		$this->assertEquals( $stockItem->getStocklevel() + 5, $actual->getStocklevel() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}

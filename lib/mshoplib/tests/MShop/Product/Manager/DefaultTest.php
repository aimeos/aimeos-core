<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Product_Manager_Default.
 */
class MShop_Product_Manager_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Product_Manager_Default( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Interface', $this->_object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$item->setId( null );
		$item->setCode( 'CNC unit test' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit save test' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );
		$this->assertEquals( $item->getSuppliercode(), $itemSaved->getSuppliercode() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );
		$this->assertEquals( $itemExp->getSuppliercode(), $itemUpd->getSuppliercode() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testSaveItemSitecheck()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->_editor ) );
		$search->setSlice(0, 1);
		$products = $manager->searchItems( $search );

		if( ( $item = reset( $products ) ) === false ) {
			throw new Exception( 'No product found' );
		}

		$item->setId( null );
		$item->setCode( 'unittest' );

		$manager->saveItem( $item );
		$manager->getItem( $item->getId() );
		$manager->deleteItem( $item->getId() );

		$this->setExpectedException( 'MShop_Exception' );
		$manager->getItem( $item->getId() );
	}


	public function testGetItem()
	{
		$domains = array('text','product', 'price', 'media','attribute');

		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->_object->searchItems( $search, $domains );

		if( ( $product = reset( $products ) ) === false ) {
			throw new Exception( sprintf( 'Found no Productitem with text "%1$s"', 'Cafe Noire Cappuccino' ) );
		}

		$this->assertEquals( $product, $this->_object->getItem( $product->getId(), $domains ) );
		$this->assertEquals( 6, count( $product->getRefItems( 'text' ) ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testCreateSearch()
	{
		$search = $this->_object->createSearch();
		$this->assertInstanceOf('MW_Common_Criteria_SQL', $search);
	}


	public function testSearchItems()
	{
		$total = 0;
		$listManager = $this->_object->getSubManager( 'list' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.type.domain', 'product' ),
			$search->compare( '==', 'product.list.type.code', 'suggestion' ),
			$search->compare( '==', 'product.list.datestart', null ),
			$search->compare( '==', 'product.list.dateend', null ),
			$search->compare( '!=', 'product.list.config', null ),
			$search->compare( '==', 'product.list.position', 0 ),
			$search->compare( '==', 'product.list.status', 1 ),
			$search->compare( '==', 'product.list.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$results = $listManager->searchItems( $search );
		if( ( $listItem = reset( $results ) ) === false ) {
			throw new Exception( 'No list item found' );
		}

		$listTypeManager = $listManager->getSubManager( 'type' );

		$search = $listTypeManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.type.domain', 'product' ),
			$search->compare( '==', 'product.list.type.code', 'suggestion' ),
			$search->compare( '==', 'product.list.type.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$results = $listTypeManager->searchItems( $search );
		if( ( $listTypeItem = reset( $results ) ) === false ) {
			throw new Exception( 'No list type item found' );
		}

		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.id', null );
		$expr[] = $search->compare( '!=', 'product.siteid', null );
		$expr[] = $search->compare( '!=', 'product.typeid', null );
		$expr[] = $search->compare( '==', 'product.code', 'CNE' );
		$expr[] = $search->compare( '==', 'product.suppliercode', 'unitSupplier' );
		$expr[] = $search->compare( '==', 'product.label', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '==', 'product.datestart', null );
		$expr[] = $search->compare( '==', 'product.dateend', null );
		$expr[] = $search->compare( '==', 'product.status', 1 );
		$expr[] = $search->compare( '>=', 'product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.editor', $this->_editor );

		$param = array( 'product', $listTypeItem->getId(), array( $listItem->getRefId() ) );
		$expr[] = $search->compare( '>', $search->createFunction( 'product.contains', $param ), 0 );

		$expr[] = $search->compare( '!=', 'product.type.id', null );
		$expr[] = $search->compare( '!=', 'product.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.type.code', 'default' );
		$expr[] = $search->compare( '==', 'product.type.label', 'Article' );
		$expr[] = $search->compare( '==', 'product.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.type.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'product.list.id', null );
		$expr[] = $search->compare( '!=', 'product.list.siteid', null );
		$expr[] = $search->compare( '!=', 'product.list.parentid', null );
		$expr[] = $search->compare( '!=', 'product.list.typeid', null );
		$expr[] = $search->compare( '==', 'product.list.domain', 'product' );
		$expr[] = $search->compare( '>', 'product.list.refid', 0 );
		$expr[] = $search->compare( '==', 'product.list.datestart', null );
		$expr[] = $search->compare( '==', 'product.list.dateend', null );
		$expr[] = $search->compare( '!=', 'product.list.config', null );
		$expr[] = $search->compare( '==', 'product.list.position', 0 );
		$expr[] = $search->compare( '==', 'product.list.status', 1 );
		$expr[] = $search->compare( '==', 'product.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'product.list.type.id', null );
		$expr[] = $search->compare( '!=', 'product.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.list.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.list.type.code', 'suggestion' );
		$expr[] = $search->compare( '==', 'product.list.type.label', 'Suggestion' );
		$expr[] = $search->compare( '==', 'product.list.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.list.type.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'product.stock.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.siteid', null );
		$expr[] = $search->compare( '!=', 'product.stock.productid', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouseid', null );
		$expr[] = $search->compare( '==', 'product.stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'product.stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.stock.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'product.stock.warehouse.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouse.siteid', null );
		$expr[] = $search->compare( '==', 'product.stock.warehouse.code', 'unit_warehouse1' );
		$expr[] = $search->compare( '==', 'product.stock.warehouse.editor', $this->_editor );


		$search->setConditions( $search->combine('&&', $expr) );
		$search->setSlice(0, 1);

		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->_editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 28, $total );


		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'product.code', array('CNC', 'CNE') ),
			$search->compare( '==', 'product.editor', $this->_editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array( 'media' ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsLimit()
	{
		$start = 0;
		$numproducts = 0;

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', 'core:unittest' ) );
		$search->setSlice( $start, 5 );

		do
		{
			$result = $this->_object->searchItems( $search );

			foreach ( $result as $item ) {
				$numproducts++;
			}

			$count = count( $result );
			$start += $count;
			$search->setSlice( $start, 5 );
		}
		while( $count > 0 );

		$this->assertEquals( 28, $numproducts );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('stock') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('stock', 'Default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list', 'Default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('list', 'unknown');
	}
}

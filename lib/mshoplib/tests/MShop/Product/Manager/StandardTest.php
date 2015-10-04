<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 */


/**
 * Test class for MShop_Product_Manager_Standard.
 */
class MShop_Product_Manager_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = new MShop_Product_Manager_Standard( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Iface', $this->object->createItem() );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No product item found' );
		}

		$item->setId( null );
		$item->setCode( 'CNC unit test' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unit save test' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


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
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
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
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSaveItemSitecheck()
	{
		$manager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 1 );
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
		$domains = array( 'text', 'product', 'price', 'media', 'attribute' );

		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->object->searchItems( $search, $domains );

		if( ( $product = reset( $products ) ) === false ) {
			throw new Exception( sprintf( 'Found no Productitem with text "%1$s"', 'Cafe Noire Cappuccino' ) );
		}

		$this->assertEquals( $product, $this->object->getItem( $product->getId(), $domains ) );
		$this->assertEquals( 6, count( $product->getRefItems( 'text' ) ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attribute );
		}
	}


	public function testCreateSearch()
	{
		$search = $this->object->createSearch();
		$this->assertInstanceOf( 'MW_Common_Criteria_SQL', $search );
	}


	public function testSearchItems()
	{
		$total = 0;
		$listManager = $this->object->getSubManager( 'lists' );

		$search = $listManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.list.type.domain', 'product' ),
			$search->compare( '==', 'product.list.type.code', 'suggestion' ),
			$search->compare( '==', 'product.list.datestart', null ),
			$search->compare( '==', 'product.list.dateend', null ),
			$search->compare( '!=', 'product.list.config', null ),
			$search->compare( '==', 'product.list.position', 0 ),
			$search->compare( '==', 'product.list.status', 1 ),
			$search->compare( '==', 'product.list.editor', $this->editor ),
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
			$search->compare( '==', 'product.list.type.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$results = $listTypeManager->searchItems( $search );
		if( ( $listTypeItem = reset( $results ) ) === false ) {
			throw new Exception( 'No list type item found' );
		}

		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'product.id', null );
		$expr[] = $search->compare( '!=', 'product.siteid', null );
		$expr[] = $search->compare( '!=', 'product.typeid', null );
		$expr[] = $search->compare( '==', 'product.code', 'CNE' );
		$expr[] = $search->compare( '==', 'product.suppliercode', 'unitSupplier' );
		$expr[] = $search->compare( '==', 'product.label', 'Cafe Noire Expresso' );
		$expr[] = $search->compare( '~=', 'product.config', 'css-class' );
		$expr[] = $search->compare( '==', 'product.datestart', null );
		$expr[] = $search->compare( '==', 'product.dateend', null );
		$expr[] = $search->compare( '==', 'product.status', 1 );
		$expr[] = $search->compare( '>=', 'product.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'product.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.editor', $this->editor );

		$param = array( 'product', $listTypeItem->getId(), array( $listItem->getRefId() ) );
		$expr[] = $search->compare( '>', $search->createFunction( 'product.contains', $param ), 0 );

		$expr[] = $search->compare( '!=', 'product.type.id', null );
		$expr[] = $search->compare( '!=', 'product.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.type.code', 'default' );
		$expr[] = $search->compare( '==', 'product.type.label', 'Article' );
		$expr[] = $search->compare( '==', 'product.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.type.editor', $this->editor );

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
		$expr[] = $search->compare( '==', 'product.list.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.list.type.id', null );
		$expr[] = $search->compare( '!=', 'product.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'product.list.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'product.list.type.code', 'suggestion' );
		$expr[] = $search->compare( '==', 'product.list.type.label', 'Suggestion' );
		$expr[] = $search->compare( '==', 'product.list.type.status', 1 );
		$expr[] = $search->compare( '==', 'product.list.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.stock.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.siteid', null );
		$expr[] = $search->compare( '!=', 'product.stock.productid', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouseid', null );
		$expr[] = $search->compare( '==', 'product.stock.stocklevel', 1000 );
		$expr[] = $search->compare( '==', 'product.stock.dateback', '2010-04-01 00:00:00' );
		$expr[] = $search->compare( '==', 'product.stock.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'product.stock.warehouse.id', null );
		$expr[] = $search->compare( '!=', 'product.stock.warehouse.siteid', null );
		$expr[] = $search->compare( '==', 'product.stock.warehouse.code', 'default' );
		$expr[] = $search->compare( '==', 'product.stock.warehouse.editor', $this->editor );


		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$search->setSlice( 0, 10 );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 10, count( $results ) );
		$this->assertEquals( 28, $total );


		$search = $this->object->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array( 'media' ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsLimit()
	{
		$start = 0;
		$numproducts = 0;

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', 'core:unittest' ) );
		$search->setSlice( $start, 5 );

		do
		{
			$result = $this->object->searchItems( $search );

			foreach( $result as $item ) {
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
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'stock' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'stock', 'Standard' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}

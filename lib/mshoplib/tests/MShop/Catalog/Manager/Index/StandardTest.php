<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Standard.
 */
class MShop_Catalog_Manager_Index_StandardTest extends PHPUnit_Framework_TestCase
{
	private static $products;
	private $context;
	private $object;
	private $editor = '';


	public static function setUpBeforeClass()
	{
		$context = TestHelper::getContext();

		$manager = new MShop_Catalog_Manager_Index_Standard( $context );
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $context->getEditor() ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text', 'product' ) );

		if( count( $result ) !== 2 ) {
			throw new Exception( 'Products not available' );
		}

		foreach( $result as $item )
		{
			self::$products[$item->getCode()] = $item;
			$manager->saveItem( $item );
		}
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->editor = $this->context->getEditor();
		$this->object = new MShop_Catalog_Manager_Index_Standard( $this->context );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Iface', $this->object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Iface', $this->object->createSearch() );
	}


	public function testAggregate()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No attribute found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'catalog.index.attribute.id' );

		$this->assertEquals( 12, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 4, $result[$item->getId()] );
	}


	public function testGetItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$item = self::$products['CNE'];

		$context = $this->context;
		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();

		$sqlAttribute = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_attribute" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlCatalog = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_catalog" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlPrice = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_price" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlText = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_text" WHERE "siteid" = ? AND "prodid" = ?';

		$this->object->saveItem( $item );

		$cntAttributeA = $this->getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalogA = $this->getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPriceA = $this->getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntTextA = $this->getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );


		$this->object->deleteItem( $item->getId() );

		$cntAttributeB = $this->getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalogB = $this->getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPriceB = $this->getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntTextB = $this->getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );


		// recreate index for CNE
		$this->object->saveItem( $item );


		$this->assertEquals( 7, $cntAttributeA );
		$this->assertEquals( 5, $cntCatalogA );
		$this->assertEquals( 2, $cntPriceA );
		$this->assertEquals( 10, $cntTextA );

		$this->assertEquals( 0, $cntAttributeB );
		$this->assertEquals( 0, $cntCatalogB );
		$this->assertEquals( 0, $cntPriceB );
		$this->assertEquals( 0, $cntTextB );
	}


	public function testSaveDeleteItemNoName()
	{
		$context = $this->context;
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$result = $productManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'Product not available' );
		}


		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();

		$sqlProd = 'SELECT "value" FROM "mshop_catalog_index_text"
			WHERE "siteid" = ? AND "prodid" = ? AND type = \'name\' AND domain = \'product\'';
		$sqlAttr = 'SELECT "value" FROM "mshop_catalog_index_text"
			WHERE "siteid" = ? AND "prodid" = ? AND type = \'name\' AND domain = \'attribute\'';

		$this->object->saveItem( $item );
		$attrText = $this->getValue( $dbm, $sqlAttr, 'value', $siteId, $item->getId() );
		$prodText = $this->getValue( $dbm, $sqlProd, 'value', $siteId, $item->getId() );
		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( '16 discs', $prodText );
		$this->assertEquals( 'XL', $attrText );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '~=', 'product.label', 'Cafe Noire' ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		// with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->object->searchItems( $search );
		$this->assertEquals( 22, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}


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


	public function testSearchItemsAttribute()
	{
		$context = $this->context;

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $context );
		$search = $attributeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.label', '29' ),
			$search->compare( '==', 'attribute.editor', $this->editor ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'width' ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $attrWidthItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}

		$expr = array(
			$search->compare( '==', 'attribute.label', '30' ),
			$search->compare( '==', 'attribute.editor', $this->editor ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'length' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $attrLenItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}


		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'catalog.index.attribute.id', $attrWidthItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.attribute.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );


		$attrIds = array( (int) $attrWidthItem->getId(), (int) $attrLenItem->getId() );
		$func = $search->createFunction( 'catalog.index.attributecount', array( 'variant', $attrIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count attributes
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$func = $search->createFunction( 'catalog.index.attribute.code', array( 'default', 'size' ) );
		$expr = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '~=', $func, 'x' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );
	}


	public function testSearchItemsCatalog()
	{
		$context = $this->context;

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Kaffee' ),
			$catSearch->compare( '==', 'catalog.editor', $this->editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Neu' ),
			$catSearch->compare( '==', 'catalog.editor', $this->editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$search->setSlice( 0, 1 );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );


		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'catalog.index.catalog.id', $catItem->getId() ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$conditions = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 7, $total );


		$func = $search->createFunction( 'catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$conditions = array(
			$search->compare( '>=', $func, 0 ), // position
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count categories
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsPrice()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$priceItems = self::$products['CNC']->getRefItems( 'price', 'default' );
		if( ( $priceItem = reset( $priceItems ) ) === false ) {
			throw new Exception( 'No price with type "default" available in product CNC' );
		}

		$conditions = array(
			$search->compare( '==', 'catalog.index.price.id', $priceItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.price.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );


		$func = $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$expr = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '>=', $func, '18.00' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 5, $total );
	}


	public function testSearchItemsText()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'classes/catalog/manager/index/text/name', 'Standard' );
		$object = new MShop_Catalog_Manager_Index_Standard( $context );

		$textItems = self::$products['CNC']->getRefItems( 'text', 'name' );
		if( ( $textItem = reset( $textItems ) ) === false ) {
			throw new Exception( 'No text with type "name" available in product CNC' );
		}

		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$conditions = array(
			$search->compare( '==', 'catalog.index.text.id', $textItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.text.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );


		$func = $search->createFunction( 'catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$func = $search->createFunction( 'catalog.index.text.value', array( 'unittype13', 'de', 'name', 'product' ) );
		$conditions = array(
			$search->compare( '~=', $func, 'Expr' ), // text value
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'de', 'name' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchTexts()
	{
		$context = $this->context;
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$textMgr = $this->object->getSubManager( 'text' );

		$search = $textMgr->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'unittype19', $langid, 'cafe noire cap' ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.value', array( 'unittype19', $langid, 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $textMgr->searchTexts( $search );

		$this->assertArrayHasKey( $product->getId(), $result );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
	}


	public function testOptimize()
	{
		$this->object->optimize();
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '0000-00-00 00:00:00' );
	}


	public function testRebuildIndexAll()
	{
		$config = $this->context->getConfig();

		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $manager->createSearch( true );
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		//build catalog with all products
		$config->set( 'mshop/catalog/manager/index/default/index', 'all' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//restore index with categorized products only
		$config->set( 'mshop/catalog/manager/index/default/index', 'categorized' );
		$this->object->rebuildIndex();

		$this->assertEquals( 13, count( $afterInsertAttr ) );
		$this->assertEquals( 11, count( $afterInsertPrice ) );
		$this->assertEquals( 8, count( $afterInsertText ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	public function testRebuildIndexWithList()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$afterDeleteAttr = $this->getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterDeletePrice = $this->getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterDeleteText = $this->getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterDeleteCat = $this->getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//insert cne, cnc
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->searchItems( $search );

		$this->object->rebuildIndex( $items );

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//delete cne, cnc
		foreach( $items as $item ) {
			$this->object->deleteItem( $item->getId() );
		}

		//restores catalog
		$this->object->rebuildIndex();

		//check delete
		$this->assertEquals( array(), $afterDeleteAttr );
		$this->assertEquals( array(), $afterDeletePrice );
		$this->assertEquals( array(), $afterDeleteText );
		$this->assertEquals( array(), $afterDeleteCat );

		//check inserted items
		$this->assertEquals( 2, count( $afterInsertAttr ) );
		$this->assertEquals( 2, count( $afterInsertPrice ) );
		$this->assertEquals( 2, count( $afterInsertText ) );
		$this->assertEquals( 2, count( $afterInsertCat ) );
	}


	public function testRebuildIndexCategorizedOnly()
	{
		$context = $this->context;
		$config = $context->getConfig();

		$manager = MShop_Product_Manager_Factory::createManager( $context );

		//delete whole catalog
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$config->set( 'mshop/catalog/manager/index/default/index', 'categorized' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//check inserted items
		$this->assertEquals( 7, count( $afterInsertAttr ) );
		$this->assertEquals( 7, count( $afterInsertPrice ) );
		$this->assertEquals( 4, count( $afterInsertText ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	/**
	 * Returns value of a catalog_index column.
	 *
	 * @param MW_DB_Manager_Iface $dbm Database Manager for connection
	 * @param string $sql Specified db query to find only one value
	 * @param string $column Column where to search
	 * @param integer $siteId Siteid of the db entry
	 * @param integer $productId Product id
	 * @return string $value Value returned for specified sql statement
	 * @throws Exception If column not available or error during a connection to db
	 */
	protected function getValue( MW_DB_Manager_Iface $dbm, $sql, $column, $siteId, $productId )
	{
		$config = $this->context->getConfig();

		if( $config->get( 'resource/db-product' ) === null ) {
			$dbname = $config->get( 'resource/default', 'db' );
		} else {
			$dbname = 'db-product';
		}

		$conn = $dbm->acquire( $dbname );

		try
		{
			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $siteId, MW_DB_Statement_Base::PARAM_INT );
			$stmt->bind( 2, $productId, MW_DB_Statement_Base::PARAM_INT );
			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) === false ) {
				throw new Exception( 'No rows available' );
			}

			if( !isset( $row[$column] ) ) {
				throw new Exception( sprintf( 'Column "%1$s" not available for "%2$s"', $column, $sql ) );
			}

			$value = $row[$column];

			$dbm->release( $conn, $dbname );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $value;
	}


	/**
	 * Gets product items of catalog index subdomains specified by the key.
	 *
	 * @param string $key Key for searchItems
	 * @param string $domain Subdomain of index manager
	 */
	protected function getCatalogSubDomainItems( $key, $domain )
	{
		$subIndex = $this->object->getSubManager( $domain );
		$search = $subIndex->createSearch();

		$expr = array(
			$search->compare( '!=', $key, null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );

		return $subIndex->searchItems( $search );
	}

}

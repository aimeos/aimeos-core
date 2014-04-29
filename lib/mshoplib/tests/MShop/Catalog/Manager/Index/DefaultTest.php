<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Default.
 */
class MShop_Catalog_Manager_Index_DefaultTest extends MW_Unittest_Testcase
{
	private static $_products;
	private $_context;
	private $_object;
	private $_editor = '';


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Index_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public static function setUpBeforeClass()
	{
		$context = TestHelper::getContext();

		$manager = new MShop_Catalog_Manager_Index_Default( $context );
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
			self::$_products[ $item->getCode() ] = $item;
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
		$this->_context = TestHelper::getContext();
		$this->_editor = $this->_context->getEditor();
		$this->_object = new MShop_Catalog_Manager_Index_Default( $this->_context );
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
		MShop_Factory::clear();
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Interface', $this->_object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
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


		$search = $this->_object->createSearch( true );
		$result = $this->_object->aggregate( $search, 'catalog.index.attribute.id' );

		$this->assertEquals( 11, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 3, $result[ $item->getId() ] );
	}


	public function testGetItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$item = self::$_products['CNE'];

		$context = $this->_context;
		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();

		$sqlAttribute = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_attribute" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlCatalog = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_catalog" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlPrice = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_price" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlText = 'SELECT COUNT(*) as count FROM "mshop_catalog_index_text" WHERE "siteid" = ? AND "prodid" = ?';

		$this->_object->saveItem( $item );

		$cntAttributeA = $this->_getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalogA = $this->_getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPriceA = $this->_getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntTextA = $this->_getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );


		$this->_object->deleteItem( $item->getId() );

		$cntAttributeB = $this->_getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalogB = $this->_getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPriceB = $this->_getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntTextB = $this->_getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );


		// recreate index for CNE
		$this->_object->saveItem( $item );


		$this->assertEquals( 7, $cntAttributeA );
		$this->assertEquals( 5, $cntCatalogA );
		$this->assertEquals( 7, $cntPriceA );
		$this->assertEquals( 14, $cntTextA );

		$this->assertEquals( 0, $cntAttributeB );
		$this->assertEquals( 0, $cntCatalogB );
		$this->assertEquals( 0, $cntPriceB );
		$this->assertEquals( 0, $cntTextB );
	}


	public function testSaveDeleteItemNoName()
	{
		$context = $this->_context;
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
		$sqlAttr ='SELECT "value" FROM "mshop_catalog_index_text"
			WHERE "siteid" = ? AND "prodid" = ? AND type = \'name\' AND domain = \'attribute\'';

		$this->_object->saveItem( $item );
		$attrText = $this->_getValue( $dbm, $sqlAttr, 'value', $siteId, $item->getId() );
		$prodText = $this->_getValue( $dbm, $sqlProd, 'value', $siteId, $item->getId() );
		$this->_object->deleteItem( $item->getId() );

		$this->assertEquals( 'Unterproduct 3', $prodText );
		$this->assertEquals( 'XL', $attrText );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '~=', 'product.label', 'Cafe Noire' ),
			$search->compare( '==', 'product.editor', $this->_editor ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		// with base criteria
		$search = $this->_object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'product.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->_object->searchItems( $search );
		$this->assertEquals( 14, count( $products ) );

		foreach($products as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}


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


	public function testSearchItemsAttribute()
	{
		$context = $this->_context;

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $context );
		$search = $attributeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.label', '29' ),
			$search->compare( '==', 'attribute.editor', $this->_editor ),
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
			$search->compare( '==', 'attribute.editor', $this->_editor ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'length' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $attrLenItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}


		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'catalog.index.attribute.id', $attrWidthItem->getId() ),
			$search->compare( '==', 'product.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.attribute.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null),
			$search->compare( '==', 'product.editor', $this->_editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 4, $total );


		$attrIds = array( (int) $attrWidthItem->getId(), (int) $attrLenItem->getId() );
		$func = $search->createFunction( 'catalog.index.attributecount', array( 'variant', $attrIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count attributes
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions(  $search->combine( '&&', $conditions ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$func = $search->createFunction( 'catalog.index.attribute.code', array( 'default', 'size' ) );
		$expr = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '~=', $func, 'x' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsCatalog()
	{
		$context = $this->_context;

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Kaffee' ),
			$catSearch->compare( '==', 'catalog.editor', $this->_editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Neu' ),
			$catSearch->compare( '==', 'catalog.editor', $this->_editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->_editor ) );
		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$search->setSlice( 0, 1 );

		$this->assertEquals( 1, count( $this->_object->searchItems( $search ) ) );


		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'catalog.index.catalog.id', $catItem->getId() ), // catalog ID
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$conditions = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ), // catalog ID
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 5, $total );


		$func = $search->createFunction( 'catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$conditions = array(
			$search->compare( '>=', $func, 0 ), // position
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count categories
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchItemsPrice()
	{
		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );

		$priceItems = self::$_products['CNC']->getRefItems( 'price', 'default' );
		if( ( $priceItem = reset( $priceItems ) ) === false ) {
			throw new Exception( 'No price with type "default" available in product CNC' );
		}

		$conditions = array(
			$search->compare( '==', 'catalog.index.price.id', $priceItem->getId() ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.price.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 5, $total );


		$func = $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$expr = array(
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '>=', $func, '18.00' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 4, $total );
	}


	public function testSearchItemsText()
	{
		$context = clone $this->_context;
		$context->getConfig()->set( 'classes/catalog/manager/index/text/name', 'Default' );
		$object = new MShop_Catalog_Manager_Index_Default( $context );

		$textItems = self::$_products['CNC']->getRefItems( 'text', 'name' );
		if( ( $textItem = reset( $textItems ) ) === false ) {
			throw new Exception( 'No text with type "name" available in product CNC' );
		}

		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );

		$conditions = array(
			$search->compare( '==', 'catalog.index.text.id', $textItem->getId() ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$expr = array(
			$search->compare( '!=', 'catalog.index.text.id', null ),
			$search->compare( '!=', 'catalog.index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );


		$func = $search->createFunction( 'catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->_editor )
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
			$search->compare( '==', 'product.editor', $this->_editor )
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
		$context = $this->_context;
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$textMgr = $this->_object->getSubManager('text');

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
		$this->_object->optimize();
	}


	public function testCleanupIndex()
	{
		$this->_object->cleanupIndex( '0000-00-00 00:00:00' );
	}


	public function testRebuildIndexAll()
	{
		$config = $this->_context->getConfig();

		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch( true );
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->_object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		//build catalog with all products
		$config->set( 'mshop/catalog/manager/index/default/index', 'all' );
		$this->_object->rebuildIndex();

		$afterInsertAttr = $this->_getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->_getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->_getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->_getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//restore index with categorized products only
		$config->set( 'mshop/catalog/manager/index/default/index', 'categorized' );
		$this->_object->rebuildIndex();

		$this->assertEquals( 11, count( $afterInsertAttr ) );
		$this->assertEquals( 10, count( $afterInsertPrice ) );
		$this->assertEquals( 8, count( $afterInsertText ) );
		$this->assertEquals( 6, count( $afterInsertCat ) );
	}


	public function testRebuildIndexWithList()
	{
		$manager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->_object->deleteItems( array_keys( $manager->searchItems($search) ) );

		$afterDeleteAttr = $this->_getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterDeletePrice = $this->_getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterDeleteText = $this->_getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterDeleteCat = $this->_getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//insert cne, cnc
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->searchItems( $search );

		$this->_object->rebuildIndex( $items );

		$afterInsertAttr = $this->_getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->_getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->_getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->_getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//delete cne, cnc
		foreach( $items as $item ) {
			$this->_object->deleteItem( $item->getId() );
		}

		//restores catalog
		$this->_object->rebuildIndex();

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
		$context = $this->_context;
		$config = $context->getConfig();

		$manager = MShop_Product_Manager_Factory::createManager( $context );

		//delete whole catalog
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );
		$this->_object->deleteItems( array_keys( $manager->searchItems($search) ) );

		$config->set( 'mshop/catalog/manager/index/default/index', 'categorized' );
		$this->_object->rebuildIndex();

		$afterInsertAttr = $this->_getCatalogSubDomainItems( 'catalog.index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->_getCatalogSubDomainItems( 'catalog.index.price.id', 'price' );
		$afterInsertText = $this->_getCatalogSubDomainItems( 'catalog.index.text.id', 'text' );
		$afterInsertCat = $this->_getCatalogSubDomainItems( 'catalog.index.catalog.id', 'catalog' );

		//check inserted items
		$this->assertEquals( 5, count( $afterInsertAttr ) );
		$this->assertEquals( 6, count( $afterInsertPrice ) );
		$this->assertEquals( 4, count( $afterInsertText ) );
		$this->assertEquals( 6, count( $afterInsertCat ) );
	}


	/**
	 * Returns value of a catalog_index column.
	 *
	 * @param MW_DB_Manager_Interface $dbm Database Manager for connection
	 * @param string $sql Specified db query to find only one value
	 * @param string $column Column where to search
	 * @param integer $siteId Siteid of the db entry
	 * @param integer $productId Product id
	 * @return string $value Value returned for specified sql statement
	 * @throws Exception If column not available or error during a connection to db
	 */
	protected function _getValue( MW_DB_Manager_Interface $dbm, $sql, $column, $siteId, $productId )
	{
		$value = null;
		$conn = $dbm->acquire();

		try
		{
			$stmt = $conn->create( $sql );
			$stmt->bind( 1, $siteId, MW_DB_Statement_Abstract::PARAM_INT );
			$stmt->bind( 2, $productId, MW_DB_Statement_Abstract::PARAM_INT );
			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) === false ) {
				throw new Exception( 'No rows available' );
			}

			if( !isset( $row[$column] ) ) {
				throw new Exception( sprintf( 'Column "%1$s" not available for "%2$s"', $column, $sql ) );
			}

			$value = $row[$column];

			$dbm->release( $conn );
		}
		catch( Exception $e )
		{
			$dbm->release( $conn );
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
	protected function _getCatalogSubDomainItems( $key, $domain )
	{
		$subIndex = $this->_object->getSubManager( $domain );
		$search = $subIndex->createSearch();

		$expr = array(
			$search->compare( '!=', $key, null ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );

		return $subIndex->searchItems( $search );
	}

}

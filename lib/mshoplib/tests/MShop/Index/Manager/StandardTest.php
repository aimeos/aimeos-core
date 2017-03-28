<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private static $products;
	private $context;
	private $object;
	private $editor = '';


	public static function setUpBeforeClass()
	{
		$context = \TestHelperMShop::getContext();

		$manager = new \Aimeos\MShop\Index\Manager\Standard( $context );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $context->getEditor() ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text', 'product' ) );

		if( count( $result ) !== 2 ) {
			throw new \RuntimeException( 'Products not available' );
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
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$this->object = new \Aimeos\MShop\Index\Manager\Standard( $this->context );
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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Product\\Item\\Iface', $this->object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $this->object->createSearch() );
	}


	public function testAggregate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No attribute found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.attribute.id' );

		$this->assertEquals( 13, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 4, $result[$item->getId()] );
	}


	public function testGetItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No item found' );
		}

		$item = $this->object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index', $result );
		$this->assertContains( 'index/attribute', $result );
		$this->assertContains( 'index/catalog', $result );
		$this->assertContains( 'index/price', $result );
		$this->assertContains( 'index/text', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$item = self::$products['CNE'];

		$context = $this->context;
		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();

		$sqlAttribute = 'SELECT COUNT(*) as count FROM "mshop_index_attribute" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlCatalog = 'SELECT COUNT(*) as count FROM "mshop_index_catalog" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlPrice = 'SELECT COUNT(*) as count FROM "mshop_index_price" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlText = 'SELECT COUNT(*) as count FROM "mshop_index_text" WHERE "siteid" = ? AND "prodid" = ?';

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
		$this->assertEquals( 19, $cntTextA );

		$this->assertEquals( 0, $cntAttributeB );
		$this->assertEquals( 0, $cntCatalogB );
		$this->assertEquals( 0, $cntPriceB );
		$this->assertEquals( 0, $cntTextB );
	}


	public function testSaveDeleteItemNoName()
	{
		$context = $this->context;
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'IJKL' ) );
		$result = $productManager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'Product not available' );
		}


		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();
		$langid = $context->getLocale()->getLanguageId();

		$sqlProd = 'SELECT "value" FROM "mshop_index_text"
			WHERE "siteid" = ? AND "prodid" = ? AND "langid" = \'' . $langid . '\'
				AND "type" = \'name\' AND domain = \'product\'';
		$sqlAttr = 'SELECT "value" FROM "mshop_index_text"
			WHERE "siteid" = ? AND "prodid" = ? AND type = \'name\' AND domain = \'attribute\'';

		$this->object->saveItem( $item );
		$attrText = $this->getValue( $dbm, $sqlAttr, 'value', $siteId, $item->getId() );
		$prodText = $this->getValue( $dbm, $sqlProd, 'value', $siteId, $item->getId() );
		$this->object->deleteItem( $item->getId() );

		$this->assertEquals( 'Unterproduct 3', $prodText );
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
			$search->compare( '!=', 'index.catalog.id', null ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search, [], $total );

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

		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $context );
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
			throw new \RuntimeException( 'No attribute item found' );
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
			throw new \RuntimeException( 'No attribute item found' );
		}


		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'index.attribute.id', $attrWidthItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );


		$expr = array(
			$search->compare( '!=', 'index.attribute.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );


		$attrIds = array( (int) $attrWidthItem->getId(), (int) $attrLenItem->getId() );
		$func = $search->createFunction( 'index.attributecount', array( 'variant', $attrIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count attributes
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$func = $search->createFunction( 'index.attribute.code', array( 'default', 'size' ) );
		$expr = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '~=', $func, 'x' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );
	}


	public function testSearchItemsCatalog()
	{
		$context = $this->context;

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Kaffee' ),
			$catSearch->compare( '==', 'catalog.editor', $this->editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$conditions = array(
			$catSearch->compare( '==', 'catalog.label', 'Neu' ),
			$catSearch->compare( '==', 'catalog.editor', $this->editor )
		);
		$catSearch->setConditions( $catSearch->combine( '&&', $conditions ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );
		$sortfunc = $search->createFunction( 'sort:index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$search->setSlice( 0, 1 );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );


		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );


		$conditions = array(
			$search->compare( '==', 'index.catalog.id', $catItem->getId() ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$conditions = array(
			$search->compare( '!=', 'index.catalog.id', null ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 7, $total );


		$func = $search->createFunction( 'index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$conditions = array(
			$search->compare( '>=', $func, 0 ), // position
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'index.catalogcount', array( 'default', $catIds ) );
		$conditions = array(
			$search->compare( '==', $func, 2 ), // count categories
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search, [], $total );

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
			throw new \RuntimeException( 'No price with type "default" available in product CNC' );
		}

		$conditions = array(
			$search->compare( '==', 'index.price.id', $priceItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$expr = array(
			$search->compare( '!=', 'index.price.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );


		$func = $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) );
		$expr = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '>=', $func, '18.00' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 5, $total );
	}


	public function testSearchItemsText()
	{
		$context = clone $this->context;
		$context->getConfig()->set( 'mshop/index/manager/text/name', 'Standard' );
		$object = new \Aimeos\MShop\Index\Manager\Standard( $context );

		$textItems = self::$products['CNC']->getRefItems( 'text', 'name' );
		if( ( $textItem = reset( $textItems ) ) === false ) {
			throw new \RuntimeException( 'No text with type "name" available in product CNC' );
		}

		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$conditions = array(
			$search->compare( '==', 'index.text.id', $textItem->getId() ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$expr = array(
			$search->compare( '!=', 'index.text.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 6, $total );


		$func = $search->createFunction( 'index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );


		$func = $search->createFunction( 'index.text.value', array( 'unittype13', 'de', 'name', 'product' ) );
		$conditions = array(
			$search->compare( '~=', $func, 'Expr' ), // text value
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:index.text.value', array( 'default', 'de', 'name' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );
	}


	public function testSearchTexts()
	{
		$context = $this->context;
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$textMgr = $this->object->getSubManager( 'text' );

		$search = $textMgr->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( 'unittype19', $langid, 'Cafe Noire Cap' ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'index.text.value', array( 'unittype19', $langid, 'name', 'product' ) ), '' ),
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
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}


	public function testRebuildIndexAll()
	{
		$config = $this->context->getConfig();

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch( true );
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		//build catalog with all products
		$config->set( 'mshop/index/manager/standard/index', 'all' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//restore index with categorized products only
		$config->set( 'mshop/index/manager/standard/index', 'categorized' );
		$this->object->rebuildIndex();

		$this->assertEquals( 13, count( $afterInsertAttr ) );
		$this->assertEquals( 11, count( $afterInsertPrice ) );
		$this->assertEquals( 13, count( $afterInsertText ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	public function testRebuildIndexWithList()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $this->context );
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$afterDeleteAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterDeletePrice = $this->getCatalogSubDomainItems( 'index.price.id', 'price' );
		$afterDeleteText = $this->getCatalogSubDomainItems( 'index.text.id', 'text' );
		$afterDeleteCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//insert cne, cnc
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->searchItems( $search );

		$this->object->rebuildIndex( $items );

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//delete cne, cnc
		foreach( $items as $item ) {
			$this->object->deleteItem( $item->getId() );
		}

		//restores catalog
		$this->object->rebuildIndex();

		//check delete
		$this->assertEquals( [], $afterDeleteAttr );
		$this->assertEquals( [], $afterDeletePrice );
		$this->assertEquals( [], $afterDeleteText );
		$this->assertEquals( [], $afterDeleteCat );

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

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		//delete whole catalog
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$config->set( 'mshop/index/manager/standard/index', 'categorized' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertPrice = $this->getCatalogSubDomainItems( 'index.price.id', 'price' );
		$afterInsertText = $this->getCatalogSubDomainItems( 'index.text.id', 'text' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//check inserted items
		$this->assertEquals( 7, count( $afterInsertAttr ) );
		$this->assertEquals( 7, count( $afterInsertPrice ) );
		$this->assertEquals( 7, count( $afterInsertText ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	/**
	 * Returns value of a catalog_index column.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database Manager for connection
	 * @param string $sql Specified db query to find only one value
	 * @param string $column Column where to search
	 * @param integer $siteId Siteid of the db entry
	 * @param integer $productId Product id
	 * @return string $value Value returned for specified sql statement
	 * @throws \Exception If column not available or error during a connection to db
	 */
	protected function getValue( \Aimeos\MW\DB\Manager\Iface $dbm, $sql, $column, $siteId, $productId )
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
			$stmt->bind( 1, $siteId, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$stmt->bind( 2, $productId, \Aimeos\MW\DB\Statement\Base::PARAM_INT );
			$result = $stmt->execute();

			if( ( $row = $result->fetch() ) === false ) {
				throw new \RuntimeException( 'No rows available' );
			}

			if( !isset( $row[$column] ) ) {
				throw new \RuntimeException( sprintf( 'Column "%1$s" not available for "%2$s"', $column, $sql ) );
			}

			$value = $row[$column];

			$dbm->release( $conn, $dbname );
		}
		catch( \Exception $e )
		{
			$dbm->release( $conn, $dbname );
			throw $e;
		}

		return $value;
	}


	/**
	 * Gets product items of index subdomains specified by the key.
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

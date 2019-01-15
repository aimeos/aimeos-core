<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $products;
	private $context;
	private $object;
	private $editor = '';


	public static function setUpBeforeClass()
	{
		$context = \TestHelperMShop::getContext();

		$manager = new \Aimeos\MShop\Index\Manager\Standard( $context );
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

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


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$this->object = new \Aimeos\MShop\Index\Manager\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->createSearch() );
	}


	public function testAggregate()
	{
		$manager = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No attribute found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.attribute.id' );

		$this->assertEquals( 15, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 4, $result[$item->getId()] );
	}


	public function testFindItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->findItem( 'CNE' );

		$this->assertEquals( $product, $this->object->findItem( 'CNE' ) );
	}


	public function testGetItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->findItem( 'CNE' );

		$item = $this->object->getItem( $product->getId() );
		$this->assertEquals( $product, $item );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index', $result );
		$this->assertContains( 'index/attribute', $result );
		$this->assertContains( 'index/supplier', $result );
		$this->assertContains( 'index/catalog', $result );
		$this->assertContains( 'index/price', $result );
		$this->assertContains( 'index/text', $result );
	}


	public function testGetSearchAttributes()
	{
		$attributes = $this->object->getSearchAttributes();

		foreach( $attributes as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}

		$this->assertArrayHasKey( 'index.attribute.id', $attributes );
		$this->assertArrayHasKey( 'index.catalog.id', $attributes );
		$this->assertArrayHasKey( 'index.supplier.id', $attributes );
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
		$result = $this->object->saveItem( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Item\Iface::class, $result );

		$this->assertEquals( 8, $cntAttributeA );
		$this->assertEquals( 5, $cntCatalogA );
		$this->assertEquals( 1, $cntPriceA );
		$this->assertEquals( 1, $cntTextA );

		$this->assertEquals( 0, $cntAttributeB );
		$this->assertEquals( 0, $cntCatalogB );
		$this->assertEquals( 0, $cntPriceB );
		$this->assertEquals( 0, $cntTextB );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '=~', 'product.label', 'Cafe Noire' ),
			$search->compare( '==', 'product.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$products = $this->object->searchItems( $search );
		$this->assertEquals( 8, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsAttributeId()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::create( $this->context );
		$attrWidthItem = $attributeManager->findItem( '29', [], 'product', 'width' );

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
	}


	public function testSearchItemsAttributeIdNotNull()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.attribute.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 7, $total );
	}


	public function testSearchItemsCatalog()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$catItem = $catalogManager->findItem( 'cafe' );

		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$search->setConditions( $search->compare( '==', 'product.editor', $this->editor ) );

		$sortfunc = $search->createFunction( 'sort:index.catalog:position', array( 'promotion', [$catItem->getId()] ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );
	}


	public function testSearchItemsCatalogId()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$catItem = $catalogManager->findItem( 'cafe' );

		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$total = 0;

		$conditions = array(
			$search->compare( '==', 'index.catalog.id', $catItem->getId() ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsCatalogIdNotNull()
	{
		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$total = 0;

		$conditions = array(
			$search->compare( '!=', 'index.catalog.id', null ), // catalog ID
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 8, $total );
	}


	public function testSearchItemsCatalogPosition()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$catItem = $catalogManager->findItem( 'cafe' );

		$search = $this->object->createSearch()->setSlice( 0, 1 );
		$total = 0;

		$func = $search->createFunction( 'index.catalog:position', array( 'promotion', [$catItem->getId()] ) );
		$conditions = array(
			$search->compare( '!=', $func, null ), // position
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$sortfunc = $search->createFunction( 'sort:index.catalog:position', array( 'promotion', [$catItem->getId()] ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsPrice()
	{
		$total = 0;
		$search = $this->object->createSearch()->setSlice( 0, 1 );

		$func = $search->createFunction( 'index.price:value', ['EUR'] );
		$expr = array(
			$search->compare( '>=', $func, '18.00' ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:index.price:value', ['EUR'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 5, $total );
	}


	public function testSearchItemsText()
	{
		$this->context->getConfig()->set( 'mshop/index/manager/text/name', 'Standard' );
		$object = new \Aimeos\MShop\Index\Manager\Standard( $this->context );

		$total = 0;
		$search = $object->createSearch()->setSlice( 0, 1 );

		$func = $search->createFunction( 'index.text:relevance', array( 'de', 'Cafe' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 3, $total );
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

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->createSearch( true );
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		//build catalog with all products
		$config->set( 'mshop/index/manager/standard/index', 'all' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//restore index with categorized products only
		$config->set( 'mshop/index/manager/standard/index', 'categorized' );
		$this->object->rebuildIndex();

		$this->assertEquals( 13, count( $afterInsertAttr ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	public function testRebuildIndexWithList()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );

		//delete whole catalog
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$afterDeleteAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterDeleteCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//insert cne, cnc
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->searchItems( $search );

		$this->object->rebuildIndex( $items );

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//delete cne, cnc
		foreach( $items as $item ) {
			$this->object->deleteItem( $item->getId() );
		}

		//restores catalog
		$this->object->rebuildIndex();

		//check delete
		$this->assertEquals( [], $afterDeleteAttr );
		$this->assertEquals( [], $afterDeleteCat );

		//check inserted items
		$this->assertEquals( 2, count( $afterInsertAttr ) );
		$this->assertEquals( 2, count( $afterInsertCat ) );
	}


	public function testRebuildIndexCategorizedOnly()
	{
		$context = $this->context;
		$config = $context->getConfig();

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

		//delete whole catalog
		$search = $manager->createSearch();
		$search->setSlice( 0, 0x7fffffff );
		$this->object->deleteItems( array_keys( $manager->searchItems( $search ) ) );

		$config->set( 'mshop/index/manager/standard/index', 'categorized' );
		$this->object->rebuildIndex();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		//check inserted items
		$this->assertEquals( 7, count( $afterInsertAttr ) );
		$this->assertEquals( 8, count( $afterInsertCat ) );
	}


	/**
	 * Returns value of a catalog_index column.
	 *
	 * @param \Aimeos\MW\DB\Manager\Iface $dbm Database Manager for connection
	 * @param string $sql Specified db query to find only one value
	 * @param string $column Column where to search
	 * @param string $siteId Siteid of the db entry
	 * @param string $productId Product id
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

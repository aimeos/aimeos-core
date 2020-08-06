<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MShop\Index\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $products;
	private $context;
	private $object;
	private $editor = '';


	public static function setUpBeforeClass() : void
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


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->editor = $this->context->getEditor();
		$this->object = new \Aimeos\MShop\Index\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
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

		$items = $manager->searchItems( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No attribute found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.attribute.id' )->toArray();

		$this->assertEquals( 14, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 3, $result[$item->getId()] );
	}


	public function testDeleteItems()
	{
		$this->assertEquals( $this->object, $this->object->deleteItems( [-1] ) );
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


	public function testSaveItem()
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

		$cntAttribute = $this->getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalog = $this->getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPrice = $this->getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntText = $this->getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );

		$this->assertEquals( 6, $cntAttribute );
		$this->assertEquals( 5, $cntCatalog );
		$this->assertEquals( 1, $cntPrice );
		$this->assertEquals( 2, $cntText );
	}


	public function testSaveItems()
	{
		$result = $this->object->saveItems( self::$products );
		$expected = [
			self::$products['CNC']->getId() => self::$products['CNC'],
			self::$products['CNE']->getId() => self::$products['CNE']
		];
		$this->assertEquals( $expected, $result );
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

		$result = $this->object->searchItems( $search, [], $total )->toArray();

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
		$products = $this->object->searchItems( $search )->toArray();
		$this->assertEquals( 8, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsSub()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.attribute.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '!=', 'index.supplier.id', null ),
			$search->compare( '>=', $search->createFunction( 'index.price:value', ['EUR'] ), 0 ),
			$search->compare( '>=', $search->createFunction( 'index.text:name', ['de'] ), '' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testOptimize()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->optimize() );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}


	public function testRebuildWithList()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->createSearch();

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->searchItems( $search )->toArray();

		$this->object->cleanup( date( 'Y-m-d H:i:s', time() + 1 ) )->rebuild( $items );

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		$this->assertEquals( 2, count( $afterInsertAttr ) );
		$this->assertEquals( 2, count( $afterInsertCat ) );
	}


	public function testRebuild()
	{
		$context = $this->context;
		$config = $context->getConfig();

		$this->object->cleanup( date( 'Y-m-d H:i:s', time() + 1 ) )->rebuild();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		$this->assertEquals( 7, count( $afterInsertAttr ) );
		$this->assertEquals( 9, count( $afterInsertCat ) );
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
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

			if( ( $row = $result->fetch() ) === null ) {
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

		return $subIndex->searchItems( $search )->toArray();
	}

}

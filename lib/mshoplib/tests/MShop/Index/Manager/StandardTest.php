<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
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

		$search = $productManager->filter();
		$conditions = array(
			$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
			$search->compare( '==', 'product.editor', $context->getEditor() ),
		);
		$search->setConditions( $search->and( $conditions ) );
		$result = $productManager->search( $search, array( 'attribute', 'price', 'text', 'product' ) );

		if( count( $result ) !== 2 ) {
			throw new \RuntimeException( 'Products not available' );
		}

		foreach( $result as $item )
		{
			self::$products[$item->getCode()] = $item;
			$manager->save( $item );
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
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->create() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\MW\Criteria\Iface::class, $this->object->filter() );
	}


	public function testAggregate()
	{
		$id = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute' )
			->find( 'white', [], 'product', 'color' )->getId();

		$search = $this->object->filter( true )->add( ['index.catalog.id' => null], '!=' );
		$result = $this->object->aggregate( $search, 'index.attribute.id' )->toArray();

		$this->assertEquals( 14, count( $result ) );
		$this->assertArrayHasKey( $id, $result );
		$this->assertEquals( 3, $result[$id] );
	}


	public function testAggregateMultiple()
	{
		$id = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'attribute' )
			->find( 'white', [], 'product', 'color' )->getId();

		$search = $this->object->filter( true )->add( ['index.catalog.id' => null], '!=' );
		$result = $this->object->aggregate( $search, ['product.status', 'index.attribute.id'] )->toArray();

		$this->assertEquals( 14, count( $result[1] ) );
		$this->assertArrayHasKey( $id, $result[1] );
		$this->assertEquals( 3, $result[1][$id] );
	}


	public function testAggregateMax()
	{
		$search = $this->object->filter( true );
		$search->add( $search->is( $search->make( 'index.price:value', ['EUR'] ), '!=', null ) );

		$result = $this->object->aggregate( $search, 'product.status', 'agg:index.price:value("EUR")', 'max' )->max();

		$this->assertEquals( 600, $result );
	}


	public function testAggregateMin()
	{
		$search = $this->object->filter( true );
		$search->add( $search->is( $search->make( 'index.price:value', ['EUR'] ), '!=', null ) );

		$result = $this->object->aggregate( $search, 'product.status', 'agg:index.price:value("EUR")', 'min' )->min();

		$this->assertEquals( 12, $result );
	}


	public function testDeleteItems()
	{
		$this->assertEquals( $this->object, $this->object->delete( [-1] ) );
	}


	public function testFindItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->find( 'CNE' );

		$this->assertEquals( $product, $this->object->find( 'CNE' ) );
	}


	public function testGetItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->find( 'CNE' );

		$item = $this->object->get( $product->getId() );
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


	public function testSave()
	{
		$item = self::$products['CNE'];

		$context = $this->context;
		$dbm = $context->getDatabaseManager();
		$siteId = $context->getLocale()->getSiteId();

		$sqlAttribute = 'SELECT COUNT(*) as count FROM "mshop_index_attribute" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlCatalog = 'SELECT COUNT(*) as count FROM "mshop_index_catalog" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlPrice = 'SELECT COUNT(*) as count FROM "mshop_index_price" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlText = 'SELECT COUNT(*) as count FROM "mshop_index_text" WHERE "siteid" = ? AND "prodid" = ?';

		$this->object->save( $item );

		$cntAttribute = $this->getValue( $dbm, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalog = $this->getValue( $dbm, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPrice = $this->getValue( $dbm, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntText = $this->getValue( $dbm, $sqlText, 'count', $siteId, $item->getId() );

		$this->assertEquals( 6, $cntAttribute );
		$this->assertEquals( 5, $cntCatalog );
		$this->assertEquals( 1, $cntPrice );
		$this->assertEquals( 2, $cntText );
	}


	public function testSaveMultiple()
	{
		$result = $this->object->save( self::$products );
		$expected = [
			'CNC' => self::$products['CNC'],
			'CNE' => self::$products['CNE']
		];
		$this->assertEquals( $expected, $result );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter();
		$search->slice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '=~', 'product.label', 'Cafe Noire' ),
			$search->compare( '==', 'product.editor', $this->editor ),
		);
		$search->setConditions( $search->and( $expr ) );

		$result = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsBase()
	{
		$search = $this->object->filter( true );
		$conditions = array(
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '==', 'product.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->and( $conditions ) );
		$products = $this->object->search( $search )->toArray();
		$this->assertEquals( 8, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchItemsSub()
	{
		$total = 0;
		$search = $this->object->filter();
		$search->slice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.attribute.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '!=', 'index.supplier.id', null ),
			$search->compare( '>=', $search->make( 'index.price:value', ['EUR'] ), 0 ),
			$search->compare( '>=', $search->make( 'index.text:name', ['de'] ), '' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->and( $expr ) );
		$result = $this->object->search( $search, [], $total )->toArray();

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


	public function testRate()
	{
		$this->assertEquals( $this->object, $this->object->rate( -1, 0, 0 ) );
	}


	public function testRebuildWithList()
	{
		$manager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$search = $manager->filter();

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->search( $search )->toArray();

		$this->object->cleanup( date( 'Y-m-d H:i:s', time() + 1 ) )->rebuild( $items );

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		$this->assertEquals( 2, count( $afterInsertAttr ) );
		$this->assertEquals( 2, count( $afterInsertCat ) );
	}


	public function testRebuild()
	{
		$this->object->cleanup( date( 'Y-m-d H:i:s', time() + 1 ) )->rebuild();

		$afterInsertAttr = $this->getCatalogSubDomainItems( 'index.attribute.id', 'attribute' );
		$afterInsertCat = $this->getCatalogSubDomainItems( 'index.catalog.id', 'catalog' );

		$this->assertEquals( 13, count( $afterInsertAttr ) );
		$this->assertEquals( 9, count( $afterInsertCat ) );
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testStock()
	{
		$this->assertEquals( $this->object, $this->object->stock( -1, 0 ) );
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
		$search = $subIndex->filter();

		$expr = array(
			$search->compare( '!=', $key, null ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->and( $expr ) );

		return $subIndex->search( $search )->toArray();
	}

}

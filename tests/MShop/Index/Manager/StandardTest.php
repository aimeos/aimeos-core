<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Index\Manager;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private static $products;
	private $context;
	private $object;


	public static function setUpBeforeClass() : void
	{
		$context = \TestHelper::context();
		$domains = $context->config()->get( 'mshop/index/manager/domains', [] );

		$manager = new \Aimeos\MShop\Index\Manager\Standard( $context );
		$productManager = \Aimeos\MShop::create( $context, 'product' );

		$search = $productManager->filter()->add( ['product.code' => ['CNC', 'CNE']] );
		$result = $productManager->search( $search, $domains );

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
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Product\Item\Iface::class, $this->object->create() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( \Aimeos\Base\Criteria\Iface::class, $this->object->filter() );
	}


	public function testAggregate()
	{
		$id = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' )
			->find( 'white', [], 'product', 'color' )->getId();

		$search = $this->object->filter( true )->add( ['index.catalog.id' => null], '!=' );
		$result = $this->object->aggregate( $search, 'index.attribute.id' )->toArray();

		$this->assertEquals( 14, count( $result ) );
		$this->assertArrayHasKey( $id, $result );
		$this->assertEquals( 4, $result[$id] );
	}


	public function testAggregateMultiple()
	{
		$id = \Aimeos\MShop::create( \TestHelper::context(), 'attribute' )
			->find( 'white', [], 'product', 'color' )->getId();

		$search = $this->object->filter( true )->add( ['index.catalog.id' => null], '!=' );
		$result = $this->object->aggregate( $search, ['product.status', 'index.attribute.id'] )->toArray();

		$this->assertEquals( 14, count( $result[1] ) );
		$this->assertArrayHasKey( $id, $result[1] );
		$this->assertEquals( 4, $result[1][$id] );
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
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' );

		$this->assertEquals( $product, $this->object->find( 'CNE' ) );
	}


	public function testGetItem()
	{
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNE' );

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
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}

		$this->assertArrayHasKey( 'index.attribute.id', $attributes );
		$this->assertArrayHasKey( 'index.catalog.id', $attributes );
		$this->assertArrayHasKey( 'index.supplier.id', $attributes );
	}


	public function testIterate()
	{
		$cursor = $this->object->cursor( $this->object->filter( true )->add( 'index.catalog.id', '!=', null ) );
		$products = $this->object->iterate( $cursor );

		$this->assertEquals( 8, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSave()
	{
		$item = self::$products['CNE'];

		$context = $this->context;
		$conn = $context->db( 'db-index' );
		$siteId = $context->locale()->getSiteId();

		$sqlAttribute = 'SELECT COUNT(*) as count FROM "mshop_index_attribute" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlCatalog = 'SELECT COUNT(*) as count FROM "mshop_index_catalog" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlPrice = 'SELECT COUNT(*) as count FROM "mshop_index_price" WHERE "siteid" = ? AND "prodid" = ?';
		$sqlText = 'SELECT COUNT(*) as count FROM "mshop_index_text" WHERE "siteid" = ? AND "prodid" = ?';

		$this->object->save( $item );

		$cntAttribute = $this->getValue( $conn, $sqlAttribute, 'count', $siteId, $item->getId() );
		$cntCatalog = $this->getValue( $conn, $sqlCatalog, 'count', $siteId, $item->getId() );
		$cntPrice = $this->getValue( $conn, $sqlPrice, 'count', $siteId, $item->getId() );
		$cntText = $this->getValue( $conn, $sqlText, 'count', $siteId, $item->getId() );

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
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );
		$items = $manager->search( $search, $this->context->config()->get( 'mshop/index/manager/domains', [] ) );

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

		$this->assertEquals( 15, count( $afterInsertAttr ) );
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
	 * @param \Aimeos\Base\DB\Connection\Iface $conn Database connection
	 * @param string $sql Specified db query to find only one value
	 * @param string $column Column where to search
	 * @param string $siteId Siteid of the db entry
	 * @param string $productId Product id
	 * @return string $value Value returned for specified sql statement
	 * @throws \Exception If column not available or error during a connection to db
	 */
	protected function getValue( \Aimeos\Base\DB\Connection\Iface $conn, $sql, $column, $siteId, $productId )
	{
		$stmt = $conn->create( $sql );
		$stmt->bind( 1, $siteId, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$stmt->bind( 2, $productId, \Aimeos\Base\DB\Statement\Base::PARAM_INT );
		$result = $stmt->execute();

		if( ( $row = $result->fetch() ) === null ) {
			throw new \RuntimeException( 'No rows available' );
		}

		if( !isset( $row[$column] ) ) {
			throw new \RuntimeException( sprintf( 'Column "%1$s" not available for "%2$s"', $column, $sql ) );
		}

		return $row[$column];
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
		);

		$search->setConditions( $search->and( $expr ) );

		return $subIndex->search( $search )->toArray();
	}

}

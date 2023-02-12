<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Index\Manager\Catalog;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Catalog\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->clear( array( -1 ) ) );
	}


	public function testAggregate()
	{
		$item = \Aimeos\MShop::create( $this->context, 'catalog' )->find( 'cafe' );

		$search = $this->object->filter( true );
		$result = $this->object->aggregate( $search, 'index.catalog.id' )->toArray();

		$this->assertEquals( 4, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 2, $result[$item->getId()] );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/catalog', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testIterate()
	{
		$filter = $this->object->filter()->add( 'index.catalog.id', '!=', null );

		$cursor = $this->object->cursor( $filter );
		$products = $this->object->iterate( $cursor );

		$this->assertEquals( 9, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSaveDeleteItem()
	{
		$catalogManager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$catItem = $catalogManager->find( 'cafe' );

		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( 'CNC' )->setId( null )->setCode( 'ModifiedCNC' )
			->addListItem( 'catalog', $productManager->createListItem(), $catItem );

		$product = $productManager->save( $product );

		$this->object->save( $product );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.catalog.id', $catItem->getId() ) );
		$result = $this->object->search( $search );


		$this->object->delete( $product->getId() );
		$productManager->delete( $product );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.catalog.id', $catItem->getId() ) );
		$result2 = $this->object->search( $search );


		$this->assertTrue( $result->has( $product->getId() ) );
		$this->assertFalse( $result2->has( $product->getId() ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItemsId()
	{
		$id = \Aimeos\MShop::create( $this->context, 'catalog' )->find( 'cafe' )->getId();

		$search = $this->object->filter()->add( ['index.catalog.id' => $id] );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsNoId()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '!=', 'index.catalog.id', null ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 9, count( $result ) );
	}


	public function testSearchItemsPosition()
	{
		$id = \Aimeos\MShop::create( $this->context, 'catalog' )->find( 'cafe' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.catalog:position', ['promotion', $id] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.catalog:position', ['promotion', $id] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPositionList()
	{
		$id = \Aimeos\MShop::create( $this->context, 'catalog' )->find( 'cafe' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.catalog:position', ['promotion', [$id]] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.catalog:position', ['promotion', [$id]] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPositionNoCatid()
	{
		$catalogManager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$id = $catalogManager->find( 'cafe' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.catalog:position', ['promotion'] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.catalog:position', ['promotion'] ) )] );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 3, count( $result ) );
	}


	public function testSearchItemsPositionNoParams()
	{
		$catalogManager = \Aimeos\MShop::create( $this->context, 'catalog' );
		$id = $catalogManager->find( 'cafe' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.catalog:position', [] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.catalog:position', [] ) )] );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 9, count( $result ) );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}

}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2023
 */


namespace Aimeos\MShop\Index\Manager\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Supplier\Standard( \TestHelper::context() );
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
		$item = \Aimeos\MShop::create( $this->context, 'supplier' )->find( 'unitSupplier001' );

		$search = $this->object->filter( true );
		$result = $this->object->aggregate( $search, 'index.supplier.id' )->toArray();

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 2, $result[$item->getId()] );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/supplier', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testIterate()
	{
		$id = \Aimeos\MShop::create( $this->context, 'supplier' )->find( 'unitSupplier001' )->getId();

		$filter = $this->object->filter( true );
		$filter->add( $filter->make( 'index.supplier:position', ['default', $id] ), '>=', 0 );

		$cursor = $this->object->cursor( $filter );
		$products = $this->object->iterate( $cursor );

		$this->assertEquals( 2, count( $products ) );

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
		$supplierManager = \Aimeos\MShop::create( $this->context, 'supplier' );
		$supItem = $supplierManager->find( 'unitSupplier001' );

		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( 'CNC' )->setId( null )->setCode( 'ModifiedCNC' )
			->addListItem( 'supplier', $productManager->createListItem(), $supItem );

		$product = $productManager->save( $product );

		$this->object->save( $product );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supItem->getId() ) );
		$result = $this->object->search( $search );


		$this->object->delete( $product->getId() );
		$productManager->delete( $product->getId() );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supItem->getId() ) );
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
		$id = \Aimeos\MShop::create( $this->context, 'supplier' )->find( 'unitSupplier001' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $id ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsIdNull()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '!=', 'index.supplier.id', null ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPosition()
	{
		$id = \Aimeos\MShop::create( $this->context, 'supplier' )->find( 'unitSupplier001' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.supplier:position', ['default', $id] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.supplier:position', ['default', $id] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPositionList()
	{
		$id = \Aimeos\MShop::create( $this->context, 'supplier' )->find( 'unitSupplier001' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.supplier:position', ['default', [$id]] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.supplier:position', ['default', [$id]] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsRadiusInside()
	{
		$search = $this->object->filter()->order( 'index.supplier.id' );
		$search->add( $search->make( 'index.supplier:radius', [52.5, 10, 115] ), '!=', null );

		$this->assertEquals( 2, $this->object->search( $search, [] )->count() );
	}


	public function testSearchItemsRadiusOutside()
	{
		$search = $this->object->filter()->order( 'index.supplier.id' );
		$search->add( $search->make( 'index.supplier:radius', [52.5, 10, 110] ), '!=', null );

		$this->assertEquals( 0, $this->object->search( $search, [] )->count() );
	}
}

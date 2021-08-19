<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MShop\Index\Manager\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Index\Manager\Supplier\Standard( \TestHelperMShop::getContext() );
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
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->find( 'CNC' );

		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$listManager = $supplierManager->getSubManager( 'lists' );

		$search = $listManager->filter( true );
		$search->setConditions( $search->compare( '==', 'supplier.lists.domain', 'product' ) );
		$supListItems = $listManager->search( $search )->toArray();

		if( ( $supListItem = reset( $supListItems ) ) === false ) {
			throw new \RuntimeException( 'No supplier list item found!' );
		}


		//new product item
		$product->setId( null );
		$product->setCode( 'SupplierCNC' );
		$productManager->save( $product );

		//new supplier list item
		$supListItem->setId( null );
		$supListItem->setRefId( $product->getId() );
		$listManager->save( $supListItem );

		$this->object->save( $product );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supListItem->getParentId() ) );
		$result = $this->object->search( $search )->toArray();


		$this->object->delete( $product->getId() );
		$listManager->delete( $supListItem->getId() );
		$productManager->delete( $product->getId() );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supListItem->getParentId() ) );
		$result2 = $this->object->search( $search )->toArray();


		$this->assertTrue( in_array( $product->getId(), array_keys( $result ) ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItemsId()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$id = $supplierManager->find( 'unitSupplier001' )->getId();

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
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$id = $supplierManager->find( 'unitSupplier001' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>=', $search->make( 'index.supplier:position', ['default', $id] ), 0 ) );
		$search->setSortations( [$search->sort( '+', $search->make( 'sort:index.supplier:position', ['default', $id] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPositionList()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$id = $supplierManager->find( 'unitSupplier001' )->getId();

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

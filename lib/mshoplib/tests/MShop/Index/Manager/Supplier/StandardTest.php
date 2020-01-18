<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
		$item = \Aimeos\MShop::create( $this->context, 'supplier' )->findItem( 'unitCode001' );

		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.supplier.id' );

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


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->findItem( 'CNC' );

		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$listManager = $supplierManager->getSubManager( 'lists' );

		$search = $listManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'supplier.lists.domain', 'product' ) );
		$supListItems = $listManager->searchItems( $search )->toArray();

		if( ( $supListItem = reset( $supListItems ) ) === false ) {
			throw new \RuntimeException( 'No supplier list item found!' );
		}


		//new product item
		$product->setId( null );
		$product->setCode( 'SupplierCNC' );
		$productManager->saveItem( $product );

		//new supplier list item
		$supListItem->setId( null );
		$supListItem->setRefId( $product->getId() );
		$listManager->saveItem( $supListItem );

		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supListItem->getParentId() ) );
		$result = $this->object->searchItems( $search )->toArray();


		$this->object->deleteItem( $product->getId() );
		$listManager->deleteItem( $supListItem->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supListItem->getParentId() ) );
		$result2 = $this->object->searchItems( $search )->toArray();


		$this->assertContains( $product->getId(), array_keys( $result ) );
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
		$id = $supplierManager->findItem( 'unitCode001' )->getId();

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $id ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsIdNull()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '!=', 'index.supplier.id', null ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPosition()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::create( $this->context );
		$id = $supplierManager->findItem( 'unitCode001' )->getId();

		$search = $this->object->createSearch();

		$expr = [
			$search->compare( '>=', $search->createFunction( 'index.supplier:position', ['default', $id] ), 0 ),
			$search->compare( '>=', $search->createFunction( 'index.supplier:position', ['default', [$id]] ), 0 ),
		];
		$search->setConditions( $search->combine( '&&', $expr ) );

		$search->setSortations( [
			$search->sort( '+', $search->createFunction( 'sort:index.supplier:position', ['default', [$id]] ) ),
			$search->sort( '+', $search->createFunction( 'sort:index.supplier:position', ['default', $id] ) ),
		] );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}
}

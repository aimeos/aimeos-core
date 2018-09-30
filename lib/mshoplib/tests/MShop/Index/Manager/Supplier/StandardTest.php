<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Index\Manager\Supplier;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Index\Manager\Supplier\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'supplier' );
		$item = $manager->findItem( 'unitCode001' );


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.supplier.id' );

		$this->assertEquals( 1, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 2, $result[$item->getId()] );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/supplier', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$product = $productManager->findItem( 'CNC' );

		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$listManager = $supplierManager->getSubManager( 'lists' );

		$search = $listManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'supplier.lists.domain', 'product' ) );
		$supListItems = $listManager->searchItems( $search );

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
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$listManager->deleteItem( $supListItem->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supListItem->getParentId() ) );
		$result2 = $this->object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItemsId()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$supItem = $supplierManager->findItem( 'unitCode001' );

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.supplier.id', $supItem->getId() ) ); // supplier ID
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsIdNull()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '!=', 'index.supplier.id', null ) ); // supplier ID
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsPosition()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$supItem = $supplierManager->findItem( 'unitCode001' );

		$search = $this->object->createSearch();
		$func = $search->createFunction( 'index.supplier:position', array( 'default', [$supItem->getId()] ) );
		$search->setConditions( $search->compare( '>=', $func, 0 ) ); // position

		$sortfunc = $search->createFunction( 'sort:index.supplier:position', array( 'default', [$supItem->getId()] ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsCount()
	{
		$supplierManager = \Aimeos\MShop\Supplier\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$supItem = $supplierManager->findItem( 'unitCode001' );
		$supItem2 = $supplierManager->findItem( 'unitCode002' );

		$search = $this->object->createSearch();
		$supIds = array( (int) $supItem->getId(), (int) $supItem2->getId() );
		$func = $search->createFunction( 'index.suppliercount', array( 'default', $supIds ) );
		$search->setConditions( $search->compare( '==', $func, 1 ) ); // count supplier

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}

}
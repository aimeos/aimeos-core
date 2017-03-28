<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Index\Manager\Catalog;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Catalog\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Index\Manager\Catalog\Standard( \TestHelperMShop::getContext() );
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


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'catalog' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.code', 'cafe' ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.catalog.id' );

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
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No product item with code CNE found!' );
		}

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$listManager = $catalogManager->getSubManager( 'lists' );
		$search = $listManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.lists.domain', 'product' ) );
		$catListItems = $listManager->searchItems( $search );

		if( ( $catListItem = reset( $catListItems ) ) === false ) {
			throw new \RuntimeException( 'No catalog list item found!' );
		}


		//new product item
		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );

		//new catalog list item
		$catListItem->setId( null );
		$catListItem->setRefId( $product->getId() );
		$listManager->saveItem( $catListItem );

		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.catalog.id', $catListItem->getParentId() ) );
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$listManager->deleteItem( $catListItem->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.catalog.id', $catListItem->getParentId() ) );
		$result2 = $this->object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$context = \TestHelperMShop::getContext();

		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Kaffee' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Neu' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}


		$search = $this->object->createSearch();

		$search->setConditions( $search->compare( '==', 'index.catalog.id', $catItem->getId() ) ); // catalog ID
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'index.catalog.id', null ) ); // catalog ID
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 8, count( $result ) );

		$func = $search->createFunction( 'index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setConditions( $search->compare( '>=', $func, 0 ) ); // position

		$sortfunc = $search->createFunction( 'sort:index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'index.catalogcount', array( 'default', $catIds ) );
		$search->setConditions( $search->compare( '==', $func, 2 ) ); // count categories

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}

}
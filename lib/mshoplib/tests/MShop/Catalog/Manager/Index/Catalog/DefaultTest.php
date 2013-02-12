<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 1366 2012-10-31 14:44:40Z doleiynyk $
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Catalog_Default.
 */
class MShop_Catalog_Manager_Index_Catalog_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Index_Catalog_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$catalogIndex = new MShop_Catalog_Manager_Index_Default( TestHelper::getContext() );
		$this->_object = $catalogIndex->getSubManager('catalog');
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Interface', $this->_object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testGetItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product item with code CNE found!' );
		}

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$listManager = $catalogManager->getSubManager( 'list' );
		$search = $listManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.list.domain', 'product' ) );
		$catListItems = $listManager->searchItems( $search );

		if( ( $catListItem = reset( $catListItems ) ) === false ) {
			throw new Exception( 'No catalog list item found!' );
		}


		//new product item
		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );

		//new catalog list item
		$catListItem->setId( null );
		$catListItem->setRefId( $product->getId() );
		$listManager->saveItem( $catListItem );

		$this->_object->saveItem( $product );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catListItem->getParentId() ) );
		$result = $this->_object->searchItems( $search );


		$this->_object->deleteItem( $product->getId() );
		$listManager->deleteItem( $catListItem->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catListItem->getParentId() ) );
		$result2 = $this->_object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testSearchItems()
	{
		$context = TestHelper::getContext();

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );
		$catSearch = $catalogManager->createSearch();
		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Kaffee' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$catSearch->setConditions( $catSearch->compare( '==', 'catalog.label', 'Neu' ) );
		$result = $catalogManager->searchItems( $catSearch );

		if( ( $catNewItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		$search = $this->_object->createSearch();

		$search->setConditions( $search->compare( '==', 'catalog.index.catalog.id', $catItem->getId() ) ); // catalog ID
		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 2, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'catalog.index.catalog.id', null ) ); // catalog ID
		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 8, count( $result ) );

		$func = $search->createFunction( 'catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setConditions( $search->compare( '>=', $func, 0 ) ); // position

		$sortfunc = $search->createFunction( 'sort:catalog.index.catalog.position', array( 'promotion', $catItem->getId() ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 2, count( $result ) );


		$catIds = array( (int) $catItem->getId(), (int) $catNewItem->getId() );
		$func = $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) );
		$search->setConditions( $search->compare( '==', $func, 2 ) ); // count categories

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );
	}

}
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Frontend_Catalog_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;


	protected function setUp()
	{
		$this->_object = new Controller_Frontend_Catalog_Default( TestHelper::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCreateManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->createManager( 'product' ) );
	}


	public function testCreateCatalogFilter()
	{
		$filter = $this->_object->createCatalogFilter( true );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Compare_Interface', $filter->getConditions() );
		$this->assertEquals( 'catalog.status', $filter->getConditions()->getName() );
	}


	public function testGetCatalogPath()
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$item = $manager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );

		$list = array();
		foreach( $this->_object->getCatalogPath( $item->getChild( 0 )->getId(), array( 'text' ) ) as $item ) {
			$list[$item->getCode()] = $item;
		}

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'root', $list );
		$this->assertArrayHasKey( 'categories', $list );
	}


	public function testGetCatalogTree()
	{
		$item = $this->_object->getCatalogTree( null, array( 'text' ), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$this->assertEquals( 'Root', $item->getName() );
		$this->assertEquals( 0, count( $item->getChildren() ) );
	}


	public function testAggregateIndex()
	{
		$filter = $this->_object->createIndexFilter();
		$list = $this->_object->aggregateIndex( $filter, 'catalog.index.attribute.id' );

		$this->assertGreaterThan( 0, count( $list ) );
	}


	public function testCreateIndexFilter()
	{
		$filter = $this->_object->createIndexFilter();

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategory()
	{
		$filter = $this->_object->createIndexFilterCategory( 0 );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$list = $filter->getConditions()->getExpressions();


		if( !isset( $list[0] ) || !( $list[0] instanceof MW_Common_Criteria_Expression_Compare_Interface ) ) {
			throw new Exception( 'Wrong expression' );
		}
		$this->assertEquals( 'catalog.index.catalog.id', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );


		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategoryPosition()
	{
		$filter = $this->_object->createIndexFilterCategory( 0, 'relevance', '-', 1, 2, 'test' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.catalog.position("test",0)', $item->getName() );
		$this->assertEquals( '-', $item->getOperator() );

		$this->assertEquals( 1, $filter->getSliceStart() );
		$this->assertEquals( 2, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategoryCode()
	{
		$filter = $this->_object->createIndexFilterCategory( 0, 'code' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'product.code', $item->getName() );
	}


	public function testCreateIndexFilterCategoryName()
	{
		$filter = $this->_object->createIndexFilterCategory( 0, 'name' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateIndexFilterCategoryPrice()
	{
		$filter = $this->_object->createIndexFilterCategory( 0, 'price' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:catalog.index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateIndexFilterCategoryInvalidSortation()
	{
		$filter = $this->_object->createIndexFilterCategory( 0, 'failure' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testAddIndexFilterCategory()
	{
		$filter = $this->_object->createIndexFilter();
		$filter = $this->_object->addIndexFilterCategory( $filter, 0 );

		$list = $filter->getConditions()->getExpressions();

		if( !isset( $list[0] ) || !( $list[0] instanceof MW_Common_Criteria_Expression_Compare_Interface ) ) {
			throw new Exception( 'Wrong expression' );
		}

		$this->assertEquals( 'catalog.index.catalog.id', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testCreateIndexFilterText()
	{
		$filter = $this->_object->createIndexFilterText( 'Espresso' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$list = $filter->getConditions()->getExpressions();


		if( !isset( $list[0] ) || !( $list[0] instanceof MW_Common_Criteria_Expression_Compare_Interface ) ) {
			throw new Exception( 'Wrong expression' );
		}
		$this->assertEquals( 'catalog.index.text.relevance("default","de","Espresso")', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );


		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterTextRelevance()
	{
		$filter = $this->_object->createIndexFilterText( 'Espresso', 'relevance', '-', 1, 2, 'test' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 1, $filter->getSliceStart() );
		$this->assertEquals( 2, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterTextCode()
	{
		$filter = $this->_object->createIndexFilterText( 'Espresso', 'code' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'product.code', $item->getName() );
	}


	public function testCreateIndexFilterTextName()
	{
		$filter = $this->_object->createIndexFilterText( 'Espresso', 'name' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateIndexFilterTextPrice()
	{
		$filter = $this->_object->createIndexFilterCategory( 'Espresso', 'price' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:catalog.index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateIndexFilterTextInvalidSortation()
	{
		$filter = $this->_object->createIndexFilterText( '', 'failure' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testAddIndexFilterText()
	{
		$filter = $this->_object->createIndexFilterText( 'Espresso' );
		$filter = $this->_object->addIndexFilterText( $filter, 'Espresso' );

		$list = $filter->getConditions()->getExpressions();

		if( !isset( $list[0] ) || !( $list[0] instanceof MW_Common_Criteria_Expression_Compare_Interface ) ) {
			throw new Exception( 'Wrong expression' );
		}

		$this->assertEquals( 'catalog.index.text.relevance("default","de","Espresso")', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testGetIndexItemsCategory()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $catalogManager->createSearch();

		$search->setConditions( $search->compare( '==', 'catalog.code', 'new' ) );
		$search->setSlice( 0, 1 );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Catalog item not found' );
		}

		$total = 0;
		$filter = $this->_object->createIndexFilterCategory( $item->getId(), 'position', '+', 1, 1 );
		$results = $this->_object->getIndexItems( $filter, array(), $total );

		$this->assertEquals( 3, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testGetIndexItemsText()
	{
		$total = 0;
		$filter = $this->_object->createIndexFilterText( 'Expresso', 'relevance', '+', 0, 1, 'unittype13' );
		$results = $this->_object->getIndexItems( $filter, array(), $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testCreateTextFilter()
	{
		$filter = $this->_object->createTextFilter( 'Expresso', 'name', '+', 0, 1 );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertInstanceOf( 'MW_Common_Criteria_Expression_Combine_Interface', $filter->getConditions() );
		$this->assertEquals( 3, count( $filter->getConditions()->getExpressions() ) );
	}


	public function testGetTextListName()
	{
		$filter = $this->_object->createTextFilter( 'cafe noire', 'relevance', '-', 0, 25, 'unittype19', 'name' );
		$results = $this->_object->getTextList( $filter );

		$this->assertEquals( 1, count( $results ) );
		$this->assertContains( 'Cafe Noire Cappuccino', $results );
	}

}

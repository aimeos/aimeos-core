<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Controller_Frontend_Catalog_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('Controller_Frontend_Catalog_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	protected function setUp()
	{
		$this->_object = new Controller_Frontend_Catalog_Default( TestHelper::getContext() );
	}


	protected function tearDown()
	{
	}


	public function testGetCatalogPath()
	{
		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$item = $manager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_LIST );

		$list = array();
		foreach( $this->_object->getCatalogPath( $item->getChild(0)->getId(), array( 'text' ) ) as $item ) {
			$list[ $item->getCode() ] = $item;
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


	public function testAggregate()
	{
		$filter = $this->_object->createProductFilterDefault();
		$list = $this->_object->aggregate( $filter, 'catalog.index.attribute.id' );

		$this->assertGreaterThan( 0, count( $list ) );
	}


	public function testProductCreateFilterDefault()
	{
		$filter = $this->_object->createProductFilterDefault();

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testProductCreateFilterByCategory()
	{
		$filter = $this->_object->createProductFilterByCategory( 0 );

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


	public function testProductCreateFilterByCategoryPosition()
	{
		$filter = $this->_object->createProductFilterByCategory( 0, 'position', '-', 1, 2, 'test' );

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


	public function testCreateProductFilterByCategoryCode()
	{
		$filter = $this->_object->createProductFilterByCategory( 0, 'code' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'product.code', $item->getName() );
	}


	public function testCreateProductFilterByCategoryName()
	{
		$filter = $this->_object->createProductFilterByCategory( 0, 'name' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateProductFilterByCategoryPrice()
	{
		$filter = $this->_object->createProductFilterByCategory( 0, 'price');

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:catalog.index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateProductFilterByCategoryInvalidSortation()
	{
		$filter = $this->_object->createProductFilterByCategory( 0, 'failure' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testCreateProductFilterByText()
	{
		$filter = $this->_object->createProductFilterByText( 'Espresso' );

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


	public function testCreateProductFilterByTextRelevance()
	{
		$filter = $this->_object->createProductFilterByText( 'Espresso', 'relevance', '-', 1, 2, 'test' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.text.relevance("test","de","Espresso")', $item->getName() );
		$this->assertEquals( '+', $item->getOperator() );

		$this->assertEquals( 1, $filter->getSliceStart() );
		$this->assertEquals( 2, $filter->getSliceSize() );
	}


	public function testCreateProductFilterByTextCode()
	{
		$filter = $this->_object->createProductFilterByText( 'Espresso', 'code' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'product.code', $item->getName() );
	}


	public function testCreateProductFilterByTextName()
	{
		$filter = $this->_object->createProductFilterByText( 'Espresso', 'name' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:catalog.index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateProductFilterByTextPrice()
	{
		$filter = $this->_object->createProductFilterByCategory( 'Espresso', 'price');

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:catalog.index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateProductFilterByTextInvalidSortation()
	{
		$filter = $this->_object->createProductFilterByText( '', 'failure' );

		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testGetProductListByCategory()
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
		$filter = $this->_object->createProductFilterByCategory( $item->getId(), 'position', '+', 1, 1 );
		$results = $this->_object->getProductList( $filter, $total );

		$this->assertEquals( 2, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testgetProductListByText()
	{
		$total = 0;
		$filter = $this->_object->createProductFilterByText( 'Expresso', 'relevance', '+', 0, 1, 'unittype13' );
		$results = $this->_object->getProductList( $filter, $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testGetTextListByName()
	{
		$filter = $this->_object->createTextFilter( 'cafe noire', 'relevance', '-', 0, 25, 'unittype19', 'name' );
		$results = $this->_object->getTextList( $filter );

		$this->assertEquals( 1, count( $results ) );
		$this->assertContains( 'Cafe Noire Cappuccino', $results );
	}

}

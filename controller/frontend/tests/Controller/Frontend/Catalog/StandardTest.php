<?php

namespace Aimeos\Controller\Frontend\Catalog;


/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\Controller\Frontend\Catalog\Standard( \TestHelperFrontend::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCreateManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->createManager( 'product' ) );
	}


	public function testCreateCatalogFilter()
	{
		$filter = $this->object->createCatalogFilter( true );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Compare\\Iface', $filter->getConditions() );
		$this->assertEquals( 'catalog.status', $filter->getConditions()->getName() );
	}


	public function testGetCatalogPath()
	{
		$manager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperFrontend::getContext() );
		$item = $manager->getTree( null, array(), \Aimeos\MW\Tree\Manager\Base::LEVEL_LIST );

		$list = array();
		foreach( $this->object->getCatalogPath( $item->getChild( 0 )->getId(), array( 'text' ) ) as $item ) {
			$list[$item->getCode()] = $item;
		}

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'root', $list );
		$this->assertArrayHasKey( 'categories', $list );
	}


	public function testGetCatalogTree()
	{
		$item = $this->object->getCatalogTree( null, array( 'text' ), \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );

		$this->assertEquals( 'Root', $item->getName() );
		$this->assertEquals( 0, count( $item->getChildren() ) );
	}


	public function testAggregateIndex()
	{
		$filter = $this->object->createIndexFilter();
		$list = $this->object->aggregateIndex( $filter, 'index.attribute.id' );

		$this->assertGreaterThan( 0, count( $list ) );
	}


	public function testCreateIndexFilter()
	{
		$filter = $this->object->createIndexFilter();

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategory()
	{
		$filter = $this->object->createIndexFilterCategory( 0 );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$list = $filter->getConditions()->getExpressions();


		if( !isset( $list[0] ) || !( $list[0] instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface ) ) {
			throw new \Exception( 'Wrong expression' );
		}
		$this->assertEquals( 'index.catalog.id', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );


		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategoryPosition()
	{
		$filter = $this->object->createIndexFilterCategory( 0, 'relevance', '-', 1, 2, 'test' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:index.catalog.position("test",0)', $item->getName() );
		$this->assertEquals( '-', $item->getOperator() );

		$this->assertEquals( 1, $filter->getSliceStart() );
		$this->assertEquals( 2, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterCategoryCode()
	{
		$filter = $this->object->createIndexFilterCategory( 0, 'code' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'product.code', $item->getName() );
	}


	public function testCreateIndexFilterCategoryName()
	{
		$filter = $this->object->createIndexFilterCategory( 0, 'name' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateIndexFilterCategoryPrice()
	{
		$filter = $this->object->createIndexFilterCategory( 0, 'price' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateIndexFilterCategoryInvalidSortation()
	{
		$filter = $this->object->createIndexFilterCategory( 0, 'failure' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testAddIndexFilterCategory()
	{
		$filter = $this->object->createIndexFilter();
		$filter = $this->object->addIndexFilterCategory( $filter, 0 );

		$list = $filter->getConditions()->getExpressions();

		if( !isset( $list[0] ) || !( $list[0] instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface ) ) {
			throw new \Exception( 'Wrong expression' );
		}

		$this->assertEquals( 'index.catalog.id', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testCreateIndexFilterText()
	{
		$filter = $this->object->createIndexFilterText( 'Espresso' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$list = $filter->getConditions()->getExpressions();


		if( !isset( $list[0] ) || !( $list[0] instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface ) ) {
			throw new \Exception( 'Wrong expression' );
		}
		$this->assertEquals( 'index.text.relevance("default","de","Espresso")', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );


		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 0, $filter->getSliceStart() );
		$this->assertEquals( 100, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterTextRelevance()
	{
		$filter = $this->object->createIndexFilterText( 'Espresso', 'relevance', '-', 1, 2, 'test' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
		$this->assertEquals( 1, $filter->getSliceStart() );
		$this->assertEquals( 2, $filter->getSliceSize() );
	}


	public function testCreateIndexFilterTextCode()
	{
		$filter = $this->object->createIndexFilterText( 'Espresso', 'code' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'product.code', $item->getName() );
	}


	public function testCreateIndexFilterTextName()
	{
		$filter = $this->object->createIndexFilterText( 'Espresso', 'name' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertEquals( 'sort:index.text.value("default","de","name")', $item->getName() );
	}


	public function testCreateIndexFilterTextPrice()
	{
		$filter = $this->object->createIndexFilterCategory( 'Espresso', 'price' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );

		$sort = $filter->getSortations();
		if( ( $item = reset( $sort ) ) === false ) {
			throw new \Exception( 'Sortation not set' );
		}

		$this->assertStringStartsWith( 'sort:index.price.value("default","EUR","default")', $item->getName() );
	}


	public function testCreateIndexFilterTextInvalidSortation()
	{
		$filter = $this->object->createIndexFilterText( '', 'failure' );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testAddIndexFilterText()
	{
		$filter = $this->object->createIndexFilterText( 'Espresso' );
		$filter = $this->object->addIndexFilterText( $filter, 'Espresso' );

		$list = $filter->getConditions()->getExpressions();

		if( !isset( $list[0] ) || !( $list[0] instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface ) ) {
			throw new \Exception( 'Wrong expression' );
		}

		$this->assertEquals( 'index.text.relevance("default","de","Espresso")', $list[0]->getName() );
		$this->assertEquals( 0, $list[0]->getValue() );
		$this->assertEquals( array(), $filter->getSortations() );
	}


	public function testGetIndexItemsCategory()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( \TestHelperFrontend::getContext() );
		$search = $catalogManager->createSearch();

		$search->setConditions( $search->compare( '==', 'catalog.code', 'new' ) );
		$search->setSlice( 0, 1 );
		$items = $catalogManager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'Catalog item not found' );
		}

		$total = 0;
		$filter = $this->object->createIndexFilterCategory( $item->getId(), 'position', '+', 1, 1 );
		$results = $this->object->getIndexItems( $filter, array(), $total );

		$this->assertEquals( 3, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testGetIndexItemsText()
	{
		$total = 0;
		$filter = $this->object->createIndexFilterText( 'Expresso', 'relevance', '+', 0, 1, 'unittype13' );
		$results = $this->object->getIndexItems( $filter, array(), $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $results ) );
	}


	public function testCreateTextFilter()
	{
		$filter = $this->object->createTextFilter( 'Expresso', 'name', '+', 0, 1 );

		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Iface', $filter );
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Expression\\Combine\\Iface', $filter->getConditions() );
		$this->assertEquals( 3, count( $filter->getConditions()->getExpressions() ) );
	}


	public function testGetTextListName()
	{
		$filter = $this->object->createTextFilter( 'cafe noire', 'relevance', '-', 0, 25, 'unittype19', 'name' );
		$results = $this->object->getTextList( $filter );

		$this->assertEquals( 1, count( $results ) );
		$this->assertContains( 'Cafe Noire Cappuccino', $results );
	}

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Perf_CatalogIndexTest extends PHPUnit_Framework_TestCase
{
	private $_context;
	private $_catItem;
	private $_slizeSize = 100;


	protected function setUp()
	{
		$this->_context = TestHelper::getContext( 'unitperf' );


		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.code', 'home' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $this->_catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}


		// parser warm up so files are already parsed (same as APC is used)

		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, 10 );

		$total = 0;
		$indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );
	}


	public function testListByPos()
	{
		$catId = (int) $this->_catItem->getId();


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->createFunction( 'catalog.index.catalog.position', array( 'default', $catId ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:catalog.index.catalog.position', array( 'default', $catId ) ) ),
		);
		$search->setSortations( $sort );
		$search->setSlice( 0, 1 );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media', 'attribute' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index list by category, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByName()
	{
		$catId = (int) $this->_catItem->getId();


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.index.catalog.id', $catId ),
			$search->compare( '>=', $search->createFunction( 'catalog.index.text.value', array( 'default', 'en', 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'en', 'name', 'product' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index list by category, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByPrice()
	{
		$catId = (int) $this->_catItem->getId();


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.index.catalog.id', $catId ),
			$search->compare( '>=', $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index list by category, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategories()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.label', 'cat-1' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$catIds = array( (int) $this->_catItem->getId(), (int) $catItem->getId() );


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.index.catalog.id', (int) $this->_catItem->getId() ),
			$search->compare( '==', $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) ), 2 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:catalog.index.catalog.position', array( 'default', $catIds ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by category count, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByPrice()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
			$search->compare( '<=', $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) ), 1000 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by price range, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByText()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'default', 'en', 'pink' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:catalog.index.text.relevance', array( 'default', 'en', 'pink' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by text, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByTextByName()
	{
		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'default', 'en', 'blue' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'en', 'name', 'product' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by text, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoryPriceText()
	{
		$catId = (int) $this->_catItem->getId();


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.index.catalog.id', $catId ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'default', 'en', 'plain' ) ), 0 ),
			$search->compare( '>=', $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:catalog.index.text.relevance', array( 'default', 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by category/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoriesPriceText()
	{
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.label', 'cat-1' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new Exception( 'No catalog item found' );
		}

		$catIds = array( (int) $this->_catItem->getId(), (int) $catItem->getId() );


		$start = microtime( true );

		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $this->_context );
		$indexManager = $catalogManager->getSubManager( 'index' );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->_slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'catalog.index.catalog.id', (int) $this->_catItem->getId() ),
			$search->compare( '>=', $search->createFunction( 'catalog.index.catalogcount', array( 'default', $catIds ) ), 2 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'default', 'en', 'plain' ) ), 0 ),
			$search->compare( '>=', $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:catalog.index.text.relevance', array( 'default', 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    catalog index search by category count/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}

}

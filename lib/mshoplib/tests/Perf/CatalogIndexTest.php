<?php

namespace Aimeos\Perf;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class CatalogIndexTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $catItem;
	private $slizeSize = 100;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext( 'unitperf' );


		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.code', 'home' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $this->catItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}


		// parser warm up so files are already parsed (same as APC is used)

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, 10 );

		$total = 0;
		$indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );
	}


	public function testListByPos()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->createFunction( 'index.catalog.position', array( 'default', $catId ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:index.catalog.position', array( 'default', $catId ) ) ),
		);
		$search->setSortations( $sort );
		$search->setSlice( 0, 1 );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media', 'attribute' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByName()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '>=', $search->createFunction( 'index.text.value', array( 'default', 'en', 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:index.text.value', array( 'default', 'en', 'name', 'product' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByPrice()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '>=', $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:index.price.value', array( 'default', 'EUR', 'default' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategories()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.label', 'cat-1' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$catIds = array( (int) $this->catItem->getId(), (int) $catItem->getId() );


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', (int) $this->catItem->getId() ),
			$search->compare( '==', $search->createFunction( 'index.catalogcount', array( 'default', $catIds ) ), 2 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:index.catalog.position', array( 'default', $catIds ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category count, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByPrice()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
			$search->compare( '<=', $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) ), 1000 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '+', $search->createFunction( 'sort:index.price.value', array( 'default', 'EUR', 'default' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by price range, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByText()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( 'default', 'en', 'pink' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:index.text.relevance', array( 'default', 'en', 'pink' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by text, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByTextByName()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( 'default', 'en', 'blue' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:index.text.value', array( 'default', 'en', 'name', 'product' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by text, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoryPriceText()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( 'default', 'en', 'plain' ) ), 0 ),
			$search->compare( '>=', $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:index.text.relevance', array( 'default', 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoriesPriceText()
	{
		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::createManager( $this->context );

		$search = $catalogManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'catalog.label', 'cat-1' ) );
		$result = $catalogManager->searchItems( $search );

		if( ( $catItem = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No catalog item found' );
		}

		$catIds = array( (int) $this->catItem->getId(), (int) $catItem->getId() );


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::createManager( $this->context );
		$search = $indexManager->createSearch( true );
		$search->setSlice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', (int) $this->catItem->getId() ),
			$search->compare( '>=', $search->createFunction( 'index.catalogcount', array( 'default', $catIds ) ), 2 ),
			$search->compare( '>', $search->createFunction( 'index.text.relevance', array( 'default', 'en', 'plain' ) ), 0 ),
			$search->compare( '>=', $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) ), 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sort = array(
			$search->sort( '-', $search->createFunction( 'sort:index.text.relevance', array( 'default', 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->searchItems( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category count/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}

}

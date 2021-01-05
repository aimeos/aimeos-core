<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\Perf;


class CatalogIndexTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $catItem;
	private $slizeSize = 100;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext( 'unitperf' );


		$catalogManager = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context );
		$this->catItem = $catalogManager->getTree( null, [], \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );


		// parser warm up so files are already parsed (same as APC is used)

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, 10 );

		$total = 0;
		$indexManager->search( $search, array( 'text', 'price', 'media' ), $total );
	}


	public function testListByPos()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->make( 'index.catalog:position', array( 'default', $catId ) ), 0 ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '+', $search->make( 'sort:index.catalog:position', array( 'default', $catId ) ) ),
		);
		$search->setSortations( $sort );
		$search->slice( 0, 1 );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media', 'attribute' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByName()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '!=', $search->make( 'index.text:name', array( 'en', '' ) ), null ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '+', $search->make( 'sort:index.text:name', array( 'en', '' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testListByPrice()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '>=', $search->make( 'index.price:value', array( 'EUR' ) ), 0 ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '+', $search->make( 'sort:index.price:value', array( 'EUR' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index list by category, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategories()
	{
		$catItem = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context )->find( 'cat-1' );
		$catIds = array( (int) $this->catItem->getId(), (int) $catItem->getId() );


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->compare( '==', 'index.catalog.id', (int) $this->catItem->getId() ),
			$search->getConditions(),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '+', $search->make( 'sort:index.catalog:position', array( 'default', $catIds ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category count, sort by position (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByPrice()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '>=', $search->make( 'index.price:value', array( 'EUR' ) ), 0 ),
			$search->compare( '<=', $search->make( 'index.price:value', array( 'EUR' ) ), 1000 ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '+', $search->make( 'sort:index.price:value', array( 'EUR' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by price range, sort by price (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByText()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '!=', $search->make( 'index.text:relevance', array( 'en', 'pink' ) ), null ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '-', $search->make( 'sort:index.text:relevance', array( 'en', 'pink' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by text, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByTextByName()
	{
		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '!=', $search->make( 'index.text:relevance', array( 'en', 'blue' ) ), null ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '-', $search->make( 'sort:index.text:name', array( 'en' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by text, sort by name (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoryPriceText()
	{
		$catId = (int) $this->catItem->getId();


		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', $catId ),
			$search->compare( '!=', $search->make( 'index.text:relevance', array( 'en', 'plain' ) ), null ),
			$search->compare( '>=', $search->make( 'index.price:value', array( 'EUR' ) ), 0 ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '-', $search->make( 'sort:index.text:relevance', array( 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}


	public function testSearchByCategoriesPriceText()
	{
		$catItem = \Aimeos\MShop\Catalog\Manager\Factory::create( $this->context )->find( 'cat-1' );

		$start = microtime( true );

		$indexManager = \Aimeos\MShop\Index\Manager\Factory::create( $this->context );
		$search = $indexManager->filter( true );
		$search->slice( 0, $this->slizeSize );

		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'index.catalog.id', (int) $this->catItem->getId() ),
			$search->compare( '!=', $search->make( 'index.text:relevance', array( 'en', 'plain' ) ), null ),
			$search->compare( '>=', $search->make( 'index.price:value', array( 'EUR' ) ), 0 ),
		);
		$search->setConditions( $search->and( $expr ) );

		$sort = array(
			$search->sort( '-', $search->make( 'sort:index.text:relevance', array( 'en', 'plain' ) ) ),
		);
		$search->setSortations( $sort );

		$total = 0;
		$result = $indexManager->search( $search, array( 'text', 'price', 'media' ), $total );

		$stop = microtime( true );
		printf( "\n    index search by category count/text/price, sort by relevance (%1\$d/%2\$d): %3\$f msec\n", count( $result ), $total, ( $stop - $start ) * 1000 );
	}

}

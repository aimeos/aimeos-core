<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Index\Manager\Text\Standard( \TestHelperMShop::getContext() );
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
		$this->object->aggregate( $this->object->createSearch(), 'index.text.id' );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/text', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItemsRelevance()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:relevance', array( 'de', 'Expr' ) );
		$search->setConditions( $search->compare( '>', $func, 0 ) );

		$sortfunc = $search->createFunction( 'sort:index.text:relevance', array( 'de', 'Expr' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsName()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '=~', $func, 'cafe' ) );

		$sortfunc = $search->createFunction( 'sort:index.text:name', ['de'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$product = $productManager->findItem( 'CNC', ['text'] );

		$this->object->deleteItem( $product->getId() );
		$this->object->saveItem( $product );

		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'cafe noire expresso' ) );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );
	}


	public function testSaveDeleteItemNoName()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$product = $productManager->findItem( 'IJKL', ['text'] );

		$this->object->deleteItem( $product->getId() );
		$this->object->saveItem( $product );

		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'unterproduct 3' ) );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}

}
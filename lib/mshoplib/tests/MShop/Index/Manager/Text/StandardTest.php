<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$this->object = new \Aimeos\MShop\Index\Manager\Text\Standard( $this->context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
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

		$search->setConditions( $search->combine( '&&', [
			$search->compare( '>', $search->createFunction( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ),
			$search->compare( '>', $search->createFunction( 'index.text:relevance', ['de', 't-disc'] ), 0 ),
		] ) );

		$search->setSortations( [
			$search->sort( '+', $search->createFunction( 'sort:index.text:relevance', ['de', 'T-DISC'] ) ),
			$search->sort( '+', $search->createFunction( 'sort:index.text:relevance', ['de', 't-disc'] ) ),
		] );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSearchItemsName()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '=~', $func, 'Cafe' ) );

		$sortfunc = $search->createFunction( 'sort:index.text:name', ['de'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsUrl()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:url', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Cafe_Noire_Cappuccino' ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->findItem( 'CNC', ['text'] );

		$this->object->deleteItem( $product->getId() );
		$this->object->saveItem( $product );

		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Cafe Noire Expresso' ) );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );
	}


	public function testSaveDeleteItemNoName()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->findItem( 'IJKL', ['text'] );

		$this->object->deleteItem( $product->getId() );
		$this->object->saveItem( $product );

		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Unterproduct 3' ) );

		$this->assertEquals( 1, count( $this->object->searchItems( $search ) ) );
	}
}

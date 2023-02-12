<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Index\Manager\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Text\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->clear( array( -1 ) ) );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/text', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testIterate()
	{
		$filter = $this->object->filter( true );
		$filter->add( $filter->make( 'index.text:name', ['de'] ), '=~', 'Cafe' );

		$cursor = $this->object->cursor( $filter );
		$products = $this->object->iterate( $cursor );

		$this->assertEquals( 2, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSearchItemsRelevance()
	{
		$config = $this->context->config();
		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'sqlsrv' ) {
			$this->markTestSkipped( 'Not supported by SQL Server' );
		}

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 't-disc'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 't-disc'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsRelevanceCase()
	{
		$config = $this->context->config();
		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'sqlsrv' ) {
			$this->markTestSkipped( 'Not supported by SQL Server' );
		}

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'T-DISC'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsNoLanguage()
	{
		$config = $this->context->config();
		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'sqlsrv' ) {
			$this->markTestSkipped( 'Not supported by SQL Server' );
		}

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'language'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'language'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 3, count( $result ) );
	}


	public function testSearchItemsName()
	{
		$search = $this->object->filter();

		$func = $search->make( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '=~', $func, 'Cafe' ) );

		$sortfunc = $search->make( 'sort:index.text:name', ['de'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsUrl()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.text:url("de")', 'cafe-noire-cappuccino' ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSaveDeleteItem()
	{
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'CNC', ['text'] );

		$this->object->delete( $product->getId() );
		$this->object->save( $product );

		$search = $this->object->filter();

		$func = $search->make( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Cafe Noire Expresso' ) );

		$this->assertEquals( 1, count( $this->object->search( $search )->toArray() ) );
	}


	public function testSaveDeleteItemNoName()
	{
		$product = \Aimeos\MShop::create( $this->context, 'product' )->find( 'IJKL', ['text'] );

		$this->object->delete( $product->getId() );
		$this->object->save( $product );

		$search = $this->object->filter();

		$func = $search->make( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Unterproduct 3' ) );

		$this->assertEquals( 1, count( $this->object->search( $search )->toArray() ) );
	}
}

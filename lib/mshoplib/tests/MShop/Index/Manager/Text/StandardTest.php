<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Index\Manager\Text;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
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
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetSubManager()
	{
		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSearchItemsRelevance()
	{
		$config = $this->context->getConfig();
		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'sqlsrv' ) {
			$this->markTestSkipped( 'Not supported by SQL Server' );
		}

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 't-disc'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 't-disc'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSearchItemsRelevanceCase()
	{
		$config = $this->context->getConfig();
		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'sqlsrv' ) {
			$this->markTestSkipped( 'Not supported by SQL Server' );
		}

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'T-DISC'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 1, count( $result ) );
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
		$search->setConditions( $search->compare( '==', 'index.text:url()', 'Cafe-Noire-Cappuccino' ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->find( 'CNC', ['text'] );

		$this->object->delete( $product->getId() );
		$this->object->save( $product );

		$search = $this->object->filter();

		$func = $search->make( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Cafe Noire Expresso' ) );

		$this->assertEquals( 1, count( $this->object->search( $search )->toArray() ) );
	}


	public function testSaveDeleteItemNoName()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $this->context );
		$product = $productManager->find( 'IJKL', ['text'] );

		$this->object->delete( $product->getId() );
		$this->object->save( $product );

		$search = $this->object->filter();

		$func = $search->make( 'index.text:name', ['de'] );
		$search->setConditions( $search->compare( '==', $func, 'Unterproduct 3' ) );

		$this->assertEquals( 1, count( $this->object->search( $search )->toArray() ) );
	}
}

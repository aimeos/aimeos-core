<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2026
 */


namespace Aimeos\MShop\Index\Manager\Text;


class SQLSrvTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = clone \TestHelper::context();
		$config = $this->context->config();

		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter !== 'sqlsrv' && !str_starts_with( $this->name(), 'testGetRelevanceTerm' ) ) {
			$this->markTestSkipped( 'SQL Server specific test' );
		}

		$this->object = new \Aimeos\MShop\Index\Manager\Text\SQLSrv( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context );
	}


	public function testGetSearchAttributes()
	{
		$list = $this->object->getSearchAttributes();

		foreach( $list as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testGetRelevanceTerm()
	{
		$this->assertEquals( 'cafe noire', $this->relevanceTerm( 'Cafe Noire' ) );
	}


	public function testGetRelevanceTermFulltext()
	{
		$this->context->config()->set( 'mshop/index/manager/text/sqlsrv/fulltext', true );

		$this->assertEquals( '"cafe*" | "noire*"', $this->relevanceTerm( 'Cafe Noire' ) );
	}


	public function testGetRelevanceTermIndex()
	{
		$this->context->config()->set( 'mshop/index/manager/text/sqlsrv/fulltext', true );

		$fcn = $this->object->getSearchAttributes()['index.text:relevance']->getFunction();
		$parts = $fcn( ['de', 'Cafe'], fn( $value ) => '\'' . $value . '\'' );

		$expected = 'mindte."id" IN ( SELECT "KEY" FROM CONTAINSTABLE("mshop_index_text", "content", \'"cafe*"\') )';
		$this->assertEquals( $expected, $parts[2] );
	}


	public function testGetRelevanceTermIndexNoFulltext()
	{
		$fcn = $this->object->getSearchAttributes()['index.text:relevance']->getFunction();
		$parts = $fcn( ['de', 'Cafe'], fn( $value ) => '\'' . $value . '\'' );

		$this->assertEquals( 'CHARINDEX( \'cafe\', mindte."content" )', $parts[2] );
	}


	public function testGetRelevanceTermFulltextShort()
	{
		$this->context->config()->set( 'mshop/index/manager/text/sqlsrv/fulltext', true );

		$this->assertEquals( '"tv"', $this->relevanceTerm( 'TV' ) );
	}


	public function testSearchRelevance()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'T-DISC'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchRelevanceCase()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 't-disc'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 't-disc'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchRelevanceTerms()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'cafe noire expresso'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'cafe noire expresso'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	protected function relevanceTerm( string $value ) : string
	{
		return \Closure::bind( fn( $value ) => $this->getRelevanceTerm( $value ), $this->object, SQLSrv::class )( $value );
	}
}

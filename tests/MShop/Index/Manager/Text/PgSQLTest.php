<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2023
 */


namespace Aimeos\MShop\Index\Manager\Text;


class PgSQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$context = clone \TestHelper::context();
		$config = $context->config();

		$dbadapter = $config->get( 'resource/db-product/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter !== 'pgsql' ) {
			$this->markTestSkipped( 'PostgreSQL specific test' );
		}

		$this->object = new \Aimeos\MShop\Index\Manager\Text\PgSQL( \TestHelper::context() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetSearchAttributes()
	{
		$list = $this->object->getSearchAttributes();

		foreach( $list as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItemsRelevance()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'T-DISC'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsRelevanceCase()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 't-disc'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 't-disc'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsRelevanceTerms()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '>', $search->make( 'index.text:relevance', ['de', 'cafe noire expresso'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->make( 'sort:index.text:relevance', ['de', 'cafe noire expresso'] ) )] );

		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}
}

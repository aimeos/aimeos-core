<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020
 */


namespace Aimeos\MShop\Index\Manager\Text;


class SQLSrvTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = clone \TestHelperMShop::getContext();
		$config = $this->context->getConfig();

		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter !== 'sqlsrv' ) {
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
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItemsRelevance()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '>', $search->createFunction( 'index.text:relevance', ['de', 'T-DISC'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->createFunction( 'sort:index.text:relevance', ['de', 'T-DISC'] ) )] );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsRelevanceCase()
	{
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '>', $search->createFunction( 'index.text:relevance', ['de', 't-disc'] ), 0 ) );
		$search->setSortations( [$search->sort( '-', $search->createFunction( 'sort:index.text:relevance', ['de', 't-disc'] ) )] );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}
}

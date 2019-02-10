<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 */


namespace Aimeos\MShop\Index\Manager\Text;


class MySQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$context = clone \TestHelperMShop::getContext();
		$config = $context->getConfig();

		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter !== 'mysql' ) {
			$this->markTestSkipped( 'MySQL specific test' );
		}

		$this->object = new \Aimeos\MShop\Index\Manager\Text\MySQL( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
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

		$func = $search->createFunction( 'index.text:relevance', array( 'de', 'T-DISC' ) );
		$search->setConditions( $search->compare( '>', $func, 0 ) );

		$sortfunc = $search->createFunction( 'sort:index.text:relevance', array( 'de', 'T-DISC' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}
}

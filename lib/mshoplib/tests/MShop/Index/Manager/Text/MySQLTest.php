<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2017
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
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\MySQL', $this->object->createSearch() );
	}


	public function testSearchItemsName()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.text.name', ['de', 'Expr'] );
		$search->setConditions( $search->compare( '>', $func, 0 ) );

		$sortfunc = $search->createFunction( 'sort:index.text.name', ['de', 'Expr'] );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 1, count( $result ) );
	}
}
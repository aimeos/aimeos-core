<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2018-2018
 */


namespace Aimeos\MShop\Index\Manager;


class PgSQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor;


	protected function setUp()
	{
		$context = clone \TestHelperMShop::getContext();
		$this->editor = $context->getEditor();
		$config = $context->getConfig();

		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter !== 'pgsql' ) {
			$this->markTestSkipped( 'PostgreSQL specific test' );
		}

		$context->getConfig()->set( 'mshop/index/manager/text/name', 'PgSQL' );
		$this->object = new \Aimeos\MShop\Index\Manager\PgSQL( $context );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testSearchItemsSub()
	{
		$total = 0;
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );

		$expr = array(
			$search->compare( '!=', 'index.attribute.id', null ),
			$search->compare( '!=', 'index.catalog.id', null ),
			$search->compare( '!=', 'index.supplier.id', null ),
			$search->compare( '>=', $search->createFunction( 'index.price:value', ['EUR'] ), 0 ),
			$search->compare( '>=', $search->createFunction( 'index.text:name', ['de'] ), '' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}
}

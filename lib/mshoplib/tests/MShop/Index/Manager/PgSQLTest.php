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


	public static function setUpBeforeClass()
	{
		$context = clone \TestHelperMShop::getContext();
		$config = $context->getConfig();
		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'pgsql' )
		{
			$context->getConfig()->set( 'mshop/index/manager/text/name', 'PgSQL' );
			$manager = new \Aimeos\MShop\Index\Manager\MySQL( $context );
			$productManager = \Aimeos\MShop\Product\Manager\Factory::create( $context );

			$search = $productManager->createSearch();
			$conditions = array(
				$search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ),
				$search->compare( '==', 'product.editor', $context->getEditor() )
			);
			$search->setConditions( $search->combine( '&&', $conditions ) );
			$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text', 'product' ) );

			foreach( $result as $item )
			{
				$manager->deleteItem( $item->getId() );
				$manager->saveItem( $item );
			}
		}
	}


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


	public function testGetSearchAttributes()
	{
		$list = $this->object->getSearchAttributes();

		foreach( $list as $attribute ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSearchItemsRelevance()
	{
		$total = 0;
		$search = $this->object->createSearch()->setSlice( 0, 1 );

		$func = $search->createFunction( 'index.text:relevance', array( 'de', 'Expr' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchItemsName()
	{
		$total = 0;
		$search = $this->object->createSearch()->setSlice( 0, 1 );

		$func = $search->createFunction( 'index.text:name', array( 'de' ) );
		$conditions = array(
			$search->compare( '~=', $func, 'Noir' ), // text name
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$sortfunc = $search->createFunction( 'sort:index.text:name', array( 'de' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );

		foreach( $result as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testOptimize()
	{
		$this->object->optimize();
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Test class for \Aimeos\MShop\Index\Manager\MySQL.
 */
class MySQLTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $editor;


	public static function setUpBeforeClass()
	{
		$context = clone \TestHelperMShop::getContext();
		$config = $context->getConfig();
		$dbadapter = $config->get( 'resource/db-index/adapter', $config->get( 'resource/db/adapter' ) );

		if( $dbadapter === 'mysql' )
		{
			$context->getConfig()->set( 'mshop/index/manager/text/name', 'MySQL' );
			$manager = new \Aimeos\MShop\Index\Manager\MySQL( $context );
			$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

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

		if( $dbadapter !== 'mysql' ) {
			$this->markTestSkipped( 'MySQL specific test' );
		}

		$context->getConfig()->set( 'mshop/index/manager/text/name', 'MySQL' );
		$this->object = new \Aimeos\MShop\Index\Manager\MySQL( $context );
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


	public function testSearchItemsRelevance()
	{
		$total = 0;
		$search = $this->object->createSearch()->setSlice( 0, 1 );

		$func = $search->createFunction( 'index.text:relevance', array( 'unittype20', 'de', 'Espresso' ) );
		$conditions = array(
			$search->compare( '!=', $func, null ), // text relevance
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
			$search->compare( '~=', $func, 'noir' ), // text value
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$sortfunc = $search->createFunction( 'sort:index.text:name', array( 'de' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$result = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 2, $total );
	}


	public function testSearchTexts()
	{
		$context = \TestHelperMShop::getContext();
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$textMgr = $this->object->getSubManager( 'text', 'MySQL' );


		$search = $textMgr->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'index.text:name', array( $langid ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $textMgr->searchTexts( $search );

		$this->assertArrayHasKey( $product->getId(), $result );
		$this->assertContains( 'cafe noire cappuccino', $result );
	}


	public function testOptimize()
	{
		$this->object->optimize();
	}
}

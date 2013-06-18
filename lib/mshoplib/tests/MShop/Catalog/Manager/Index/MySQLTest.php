<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Catalog_Manager_Index_MySQL.
 */
class MShop_Catalog_Manager_Index_MySQLTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_editor;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Index_MySQLTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public static function setUpBeforeClass()
	{
		$context = clone TestHelper::getContext();
		$context->getConfig()->set( 'classes/catalog/manager/index/text/name', 'MySQL' );

		$manager = new MShop_Catalog_Manager_Index_MySQL( $context );
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

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


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = clone TestHelper::getContext();
		$context->getConfig()->set( 'classes/catalog/manager/index/text/name', 'MySQL' );

		$this->_editor = $context->getEditor();
		$config = $context->getConfig();

		$dbadapter = $config->get( 'resource/db/adapter' );

		if( $dbadapter !== 'mysql' ) {
			$this->markTestSkipped( 'MySQL specific test' );
		}

		$this->_object = new MShop_Catalog_Manager_Index_MySQL( $context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetSearchAttributes()
	{
		$list = $this->_object->getSearchAttributes();

		foreach( $list as $attribute )
		{
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );

			switch( $attribute->getCode() )
			{
				case 'catalog.index.text.relevance()':
				case 'sort:catalog.index.text.relevance()':
					$this->assertGreaterThanOrEqual( 0, strpos( $attribute->getInternalCode(), 'MATCH' ) );
			}
		}
	}


	public function testSearchItemsText()
	{
		$total = 0;
		$search = $this->_object->createSearch();
		$search->setSlice( 0, 1 );

		$func = $search->createFunction( 'catalog.index.text.relevance', array( 'unittype20', 'de', 'Espresso' ) );
		$conditions = array(
			$search->compare( '>', $func, 0 ), // text relevance
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( 'unittype20', 'de', 'Espresso' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$func = $search->createFunction( 'catalog.index.text.value', array( 'unittype19', 'de', 'name', 'product' ) );
		$conditions = array(
			$search->compare( '~=', $func, 'Noir' ), // text value
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'de', 'name' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		foreach($result as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTexts()
	{
		$context = TestHelper::getContext();
		$productManager = MShop_Product_Manager_Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$textMgr = $this->_object->getSubManager( 'text', 'MySQL' );


		$search = $textMgr->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'unittype19', $langid, 'noir cap' ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.value', array( 'unittype19', $langid, 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $textMgr->searchTexts( $search );

		$this->assertArrayHasKey( $product->getId(), $result );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
	}


	public function testOptimize()
	{
		$this->_object->optimize();
	}
}

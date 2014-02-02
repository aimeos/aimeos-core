<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Text_Default.
 */
class MShop_Catalog_Manager_Index_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	protected static $_products;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Index_Text_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	public static function setUpBeforeClass()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text', 'product' ) );

		if( count( $result ) !== 2 ) {
			throw new Exception( 'Products not available' );
		}

		foreach( $result as $item )	{
			self::$_products[ $item->getCode() ] = $item;
		}
	}

	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$catalogIndex = new MShop_Catalog_Manager_Index_Default( TestHelper::getContext() );
		$this->_object = $catalogIndex->getSubManager('text');
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
		MShop_Factory::clear();
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Interface', $this->_object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testAggregate()
	{
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'text' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'text.domain', 'attribute' ),
			$search->compare( '==', 'text.content', 'XS' ),
			$search->compare( '==', 'text.type.code', 'name' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No text item found' );
		}


		$search = $this->_object->createSearch( true );
		$result = $this->_object->aggregate( $search, 'catalog.index.text.id' );

		$this->assertEquals( 24, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( $result[ $item->getId() ], 2 );
	}


	public function testGetItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$search = $productManager->createSearch();
		$search->setSlice( 0, 1 );
		$result = $productManager->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $item );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$product = self::$_products[ 'CNC' ];

		$texts = $product->getRefItems( 'text' );
		if( ( $textItem = reset( $texts ) ) === false ) {
			throw new Exception( 'Product doesnt have any price item' );
		}


		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->_object->saveItem( $product );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result = $this->_object->searchItems( $search );


		$this->_object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result2 = $this->_object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$textItems = self::$_products['CNC']->getRefItems( 'text', 'name' );
		if( ( $textItem = reset( $textItems ) ) === false ) {
			throw new Exception( 'No text with type "name" available in product CNC' );
		}

		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'catalog.index.text.id', null ) );

		$result = $this->_object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );


		$func = $search->createFunction( 'catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setConditions( $search->compare( '>', $func, 0 ) ); // text relevance

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );

		$func = $search->createFunction( 'catalog.index.text.value', array( 'unittype13', 'de', 'name', 'product' ) );
		$search->setConditions( $search->compare( '~=', $func, 'Expr' ) ); // text value

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'de', 'name' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->_object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );
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

		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'unittype19', $langid, 'cafe noire cap' ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.value', array( 'unittype19', $langid, 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->searchTexts( $search );

		$this->assertArrayHasKey( $product->getId(), $result );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
	}

}
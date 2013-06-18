<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Attribute_Default.
 */
class MShop_Catalog_Manager_Index_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Catalog_Manager_Index_Attribute_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$catalogIndex = new MShop_Catalog_Manager_Index_Default( TestHelper::getContext() );
		$this->_object = $catalogIndex->getSubManager('attribute');
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
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Product_Item_Interface', $this->_object->createItem() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
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
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$result = $productManager->searchItems( $search, array('attribute') );

		if( ( $product = reset( $result ) ) === false ) {
			throw new Exception( 'No product item with code CNE found!' );
		}

		$attributes = $product->getRefItems( 'attribute' );
		if( ( $attrItem = reset( $attributes ) ) === false ) {
			throw new Exception( 'Product doesnt have any attribute item' );
		}

		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->_object->saveItem( $product );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.attribute.id', $attrItem->getId() ) );
		$result = $this->_object->searchItems( $search );


		$this->_object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.attribute.id', $attrItem->getId() ) );
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
		$context = TestHelper::getContext();
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $context );
		$search = $attributeManager->createSearch();

		$expr = array(
			$search->compare( '==', 'attribute.code', '30' ),
			$search->compare( '==', 'attribute.editor', $context->getEditor() ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'length' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $attrLengthItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}

		$expr = array(
			$search->compare( '==', 'attribute.code', '29' ),
			$search->compare( '==', 'attribute.editor', $context->getEditor() ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'width' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $attributeManager->searchItems( $search );

		if( ( $attrWidthItem = reset( $result ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.attribute.id', $attrWidthItem->getId() ) );

		$result = $this->_object->searchItems( $search, array() );
		$this->assertGreaterThanOrEqual( 1, count( $result ) );


		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.attribute.id', $attrLengthItem->getId() ) );

		$result = $this->_object->searchItems( $search, array() );
		$this->assertEquals( 2, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'catalog.index.attribute.id', null ) );

		$result = $this->_object->searchItems( $search, array() );
		$this->assertGreaterThanOrEqual( 2, count( $result ) );


		$attrIds = array( (int) $attrLengthItem->getId(), (int) $attrWidthItem->getId() );
		$func = $search->createFunction( 'catalog.index.attributecount', array( 'variant', $attrIds ) );
		$search->setConditions( $search->compare( '==', $func, 1 ) ); // count attributes

		$result = $this->_object->searchItems( $search, array() );
		$this->assertEquals( 0, count( $result ) );


		$func = $search->createFunction( 'catalog.index.attribute.code', array( 'default', 'size' ) );
		$search->setConditions( $search->compare( '~=', $func, 'x' ) );

		$result = $this->_object->searchItems( $search, array() );
		$this->assertEquals( 2, count( $result ) );
	}

}
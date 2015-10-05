<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Catalog\Manager\Index\Text;


/**
 * Test class for \Aimeos\MShop\Catalog\Manager\Index\Text\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	protected static $products;
	private $editor = '';


	public static function setUpBeforeClass()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text', 'product' ) );

		if( count( $result ) !== 2 ) {
			throw new \Exception( 'Products not available' );
		}

		foreach( $result as $item ) {
			self::$products[$item->getCode()] = $item;
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
		$this->editor = \TestHelper::getContext()->getEditor();

		$this->object = new \Aimeos\MShop\Catalog\Manager\Index\Text\Standard( \TestHelper::getContext() );
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


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelper::getContext(), 'text' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'text.domain', 'attribute' ),
			$search->compare( '==', 'text.content', 'XS' ),
			$search->compare( '==', 'text.type.code', 'name' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No text item found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'catalog.index.text.id' );

		$this->assertEquals( 18, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 2, $result[$item->getId()] );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Common\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelper::getContext() );
		$product = self::$products['CNC'];

		$texts = $product->getRefItems( 'text' );
		if( ( $textItem = reset( $texts ) ) === false ) {
			throw new \Exception( 'Product doesnt have any price item' );
		}


		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result2 = $this->object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$textItems = self::$products['CNC']->getRefItems( 'text', 'name' );
		if( ( $textItem = reset( $textItems ) ) === false ) {
			throw new \Exception( 'No text with type "name" available in product CNC' );
		}

		$search->setConditions( $search->compare( '==', 'catalog.index.text.id', $textItem->getId() ) );
		$result = $this->object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'catalog.index.text.id', null ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );


		$func = $search->createFunction( 'catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setConditions( $search->compare( '>', $func, 0 ) ); // text relevance

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.relevance', array( 'unittype13', 'de', 'Expr' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );

		$func = $search->createFunction( 'catalog.index.text.value', array( 'unittype13', 'de', 'name', 'product' ) );
		$search->setConditions( $search->compare( '~=', $func, 'Expr' ) ); // text value

		$sortfunc = $search->createFunction( 'sort:catalog.index.text.value', array( 'default', 'de', 'name' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertEquals( 1, count( $result ) );
	}


	public function testSearchTexts()
	{
		$context = \TestHelper::getContext();
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );

		$search = $productManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'product.code', 'CNC' ),
			$search->compare( '==', 'product.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $productManager->searchItems( $search );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \Exception( 'No product found' );
		}


		$langid = $context->getLocale()->getLanguageId();

		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '>', $search->createFunction( 'catalog.index.text.relevance', array( 'unittype19', $langid, 'cafe noire cap' ) ), 0 ),
			$search->compare( '>', $search->createFunction( 'catalog.index.text.value', array( 'unittype19', $langid, 'name', 'product' ) ), '' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->searchTexts( $search );

		$this->assertArrayHasKey( $product->getId(), $result );
		$this->assertContains( 'Cafe Noire Cappuccino', $result );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '0000-00-00 00:00:00' );
	}

}
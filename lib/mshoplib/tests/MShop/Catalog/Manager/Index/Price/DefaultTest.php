<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Catalog_Manager_Index_Price_Default.
 */
class MShop_Catalog_Manager_Index_Price_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	protected static $products;


	public static function setUpBeforeClass()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text' ) );

		if( count( $result ) !== 2 ) {
			throw new Exception( 'Products not available' );
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
		$this->object = new MShop_Catalog_Manager_Index_Price_Default( TestHelper::getContext() );
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
		$manager = MShop_Factory::createManager( TestHelper::getContext(), 'price' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', '18.00' ),
			$search->compare( '==', 'price.currencyid', 'EUR' ),
			$search->compare( '==', 'price.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No price item found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'catalog.index.price.id' );

		$this->assertEquals( 6, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 3, $result[$item->getId()] );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( TestHelper::getContext() );
		$product = clone self::$products['CNC'];

		$prices = $product->getRefItems( 'price' );
		if( ( $priceItem = reset( $prices ) ) === false ) {
			throw new Exception( 'Product doesnt have any price item' );
		}


		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.price.id', $priceItem->getId() ) );
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.index.price.id', $priceItem->getId() ) );
		$result2 = $this->object->searchItems( $search );


		$this->assertContains( $product->getId(), array_keys( $result ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$priceItems = self::$products['CNC']->getRefItems( 'price', 'default' );
		if( ( $priceItem = reset( $priceItems ) ) === false ) {
			throw new Exception( 'No price with type "default" available in product CNC' );
		}

		$search->setConditions( $search->compare( '==', 'catalog.index.price.id', $priceItem->getId() ) );
		$result = $this->object->searchItems( $search, array() );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsIdNull()
	{
		$search = $this->object->createSearch();

		$search->setConditions( $search->compare( '!=', 'catalog.index.price.id', null ) );
		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsQuantity()
	{
		$search = $this->object->createSearch();

		$search->setConditions( $search->compare( '==', 'catalog.index.price.quantity', 1 ) );
		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsQuantityValue()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$expr = array(
			$search->compare( '>=', $func, '10.00' ),
			$search->compare( '==', 'catalog.index.price.quantity', 1 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsValue()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '>=', $func, '18.00' ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsCosts()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'catalog.index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '>=', $func, '20.00' ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsRebate()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'catalog.index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '==', $func, '1.50' ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsTaxrate()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'catalog.index.price.taxrate', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '==', $func, '19.00' ) );

		$sortfunc = $search->createFunction( 'sort:catalog.index.price.taxrate', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, array() );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '0000-00-00 00:00:00' );
	}

}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Index\Manager\Price;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Price\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	protected static $products;


	public static function setUpBeforeClass()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNC', 'CNE' ) ) );
		$result = $productManager->searchItems( $search, array( 'attribute', 'price', 'text' ) );

		if( count( $result ) !== 2 ) {
			throw new \RuntimeException( 'Products not available' );
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
		$this->object = new \Aimeos\MShop\Index\Manager\Price\Standard( \TestHelperMShop::getContext() );
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
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'price' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'price.value', '18.00' ),
			$search->compare( '==', 'price.currencyid', 'EUR' ),
			$search->compare( '==', 'price.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No price item found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.price.id' );

		$this->assertEquals( 6, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 3, $result[$item->getId()] );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/price', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$product = clone self::$products['CNC'];

		$prices = $product->getRefItems( 'price' );
		if( ( $priceItem = reset( $prices ) ) === false ) {
			throw new \RuntimeException( 'Product doesnt have any price item' );
		}


		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.price.id', $priceItem->getId() ) );
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.price.id', $priceItem->getId() ) );
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

		$priceItems = self::$products['CNC']->getRefItems( 'price', 'default' );
		if( ( $priceItem = reset( $priceItems ) ) === false ) {
			throw new \RuntimeException( 'No price with type "default" available in product CNC' );
		}

		$search->setConditions( $search->compare( '==', 'index.price.id', $priceItem->getId() ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testSearchItemsIdNull()
	{
		$search = $this->object->createSearch();

		$search->setConditions( $search->compare( '!=', 'index.price.id', null ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsQuantity()
	{
		$search = $this->object->createSearch();

		$search->setConditions( $search->compare( '==', 'index.price.quantity', 1 ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsQuantityValue()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) );
		$expr = array(
			$search->compare( '>=', $func, '10.00' ),
			$search->compare( '==', 'index.price.quantity', 1 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$sortfunc = $search->createFunction( 'sort:index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsValue()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '>=', $func, '18.00' ) );

		$sortfunc = $search->createFunction( 'sort:index.price.value', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsCosts()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '>=', $func, '20.00' ) );

		$sortfunc = $search->createFunction( 'sort:index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsRebate()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '==', $func, '1.50' ) );

		$sortfunc = $search->createFunction( 'sort:index.price.costs', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsTaxrate()
	{
		$search = $this->object->createSearch();

		$func = $search->createFunction( 'index.price.taxrate', array( 'default', 'EUR', 'default' ) );
		$search->setConditions( $search->compare( '==', $func, '19.00' ) );

		$sortfunc = $search->createFunction( 'sort:index.price.taxrate', array( 'default', 'EUR', 'default' ) );
		$search->setSortations( array( $search->sort( '+', $sortfunc ) ) );

		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}

}
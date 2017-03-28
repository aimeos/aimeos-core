<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Index\Manager\Attribute;


/**
 * Test class for \Aimeos\MShop\Index\Manager\Attribute\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Index\Manager\Attribute\Standard( \TestHelperMShop::getContext() );
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
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'attribute' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'white' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.type.code', 'color' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No attribute item found' );
		}


		$search = $this->object->createSearch( true );
		$result = $this->object->aggregate( $search, 'index.attribute.id' );

		$this->assertEquals( 13, count( $result ) );
		$this->assertArrayHasKey( $item->getId(), $result );
		$this->assertEquals( 4, $result[$item->getId()] );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/attribute', $result );
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
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNC' ) );

		$result = $productManager->searchItems( $search, array( 'attribute' ) );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No product item with code CNE found!' );
		}

		$attributes = $product->getRefItems( 'attribute' );
		if( ( $attrItem = reset( $attributes ) ) === false ) {
			throw new \RuntimeException( 'Product doesnt have any attribute item' );
		}

		$product->setId( null );
		$product->setCode( 'ModifiedCNC' );
		$productManager->saveItem( $product );
		$this->object->saveItem( $product );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrItem->getId() ) );
		$result = $this->object->searchItems( $search );


		$this->object->deleteItem( $product->getId() );
		$productManager->deleteItem( $product->getId() );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrItem->getId() ) );
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
		$context = \TestHelperMShop::getContext();
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( $context );
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
			throw new \RuntimeException( 'No attribute item found' );
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
			throw new \RuntimeException( 'No attribute item found' );
		}


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrWidthItem->getId() ) );

		$result = $this->object->searchItems( $search, [] );
		$this->assertGreaterThanOrEqual( 1, count( $result ) );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrLengthItem->getId() ) );

		$result = $this->object->searchItems( $search, [] );
		$this->assertEquals( 3, count( $result ) );

		$search->setConditions( $search->compare( '!=', 'index.attribute.id', null ) );

		$result = $this->object->searchItems( $search, [] );
		$this->assertGreaterThanOrEqual( 2, count( $result ) );


		$attrIds = array( (int) $attrLengthItem->getId(), (int) $attrWidthItem->getId() );
		$func = $search->createFunction( 'index.attributecount', array( 'variant', $attrIds ) );
		$search->setConditions( $search->compare( '==', $func, 2 ) ); // count attributes
		$search->setSortations( array( $search->sort( '+', 'product.code' ) ) );
		$result = $this->object->searchItems( $search, [] );

		if( ( $product = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No product found' );
		}

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'CNE', $product->getCode() );


		$func = $search->createFunction( 'index.attribute.code', array( 'default', 'size' ) );
		$search->setConditions( $search->compare( '~=', $func, 'x' ) );

		$result = $this->object->searchItems( $search, [] );
		$this->assertEquals( 4, count( $result ) );
	}


	public function testCleanupIndex()
	{
		$this->object->cleanupIndex( '1970-01-01 00:00:00' );
	}

}
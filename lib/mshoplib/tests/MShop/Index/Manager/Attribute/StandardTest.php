<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Index\Manager\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Index\Manager\Attribute\Standard( \TestHelperMShop::getContext() );
	}


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

		$this->assertEquals( 15, count( $result ) );
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


	public function testSearchItemsIdWidth()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$attrWidthItem = $attributeManager->findItem( '29', [], 'product', 'width' );

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrWidthItem->getId() ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 1, count( $result ) );
	}


	public function testSearchItemsIdLength()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$attrLengthItem = $attributeManager->findItem( '30', [], 'product', 'length' );


		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrLengthItem->getId() ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 3, count( $result ) );
	}


	public function testSearchItemsNoId()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '!=', 'index.attribute.id', null ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsCount()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$attrLengthItem = $attributeManager->findItem( '30', [], 'product', 'length' );
		$attrWidthItem = $attributeManager->findItem( '29', [], 'product', 'width' );

		$search = $this->object->createSearch();
		$attrIds = array( (int) $attrLengthItem->getId(), (int) $attrWidthItem->getId() );
		$func = $search->createFunction( 'index.attributecount', array( 'variant', $attrIds ) );
		$search->setConditions( $search->compare( '==', $func, 2 ) ); // count attributes
		$search->setSortations( array( $search->sort( '+', 'product.code' ) ) );
		$result = $this->object->searchItems( $search, [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'CNE', reset( $result )->getCode() );
	}


	public function testSearchItemsAll()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$attrLengthItem = $attributeManager->findItem( '30', [], 'product', 'length' );
		$attrWidthItem = $attributeManager->findItem( '29', [], 'product', 'width' );

		$search = $this->object->createSearch();
		$attrIds = [(int) $attrLengthItem->getId(), (int) $attrWidthItem->getId()];
		$func = $search->createFunction( 'index.attribute:all', [$attrIds] );
		$search->setConditions( $search->compare( '!=', $func, null ) );
		$search->setSortations( array( $search->sort( '+', 'product.code' ) ) );
		$result = $this->object->searchItems( $search );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'CNE', reset( $result )->getCode() );
	}


	public function testSearchItemsCode()
	{
		$attributeManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$search = $this->object->createSearch();
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
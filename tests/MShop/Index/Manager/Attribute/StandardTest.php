<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Index\Manager\Attribute;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$this->object = new \Aimeos\MShop\Index\Manager\Attribute\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->clear( array( -1 ) ) );
	}


	public function testAggregate()
	{
		$item = \Aimeos\MShop::create( $this->context, 'attribute' )->find( 'white', [], 'product', 'color' );

		$search = $this->object->filter( true );
		$result = $this->object->aggregate( $search, 'index.attribute.id' );

		$this->assertEquals( 15, $result->count() );
		$this->assertEquals( 5, $result->get( $item->getId() ) );
	}


	public function testAggregateMultiple()
	{
		$item = \Aimeos\MShop::create( $this->context, 'attribute' )->find( 'white', [], 'product', 'color' );

		$search = $this->object->filter( true );
		$result = $this->object->aggregate( $search, ['product.status', 'index.attribute.id'] );

		$this->assertEquals( 15, count( $result[1] ) );
		$this->assertEquals( 5, $result[1][$item->getId()] );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'index/attribute', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $attribute );
		}
	}


	public function testSaveDeleteItem()
	{
		$productManager = \Aimeos\MShop::create( $this->context, 'product' );
		$product = $productManager->find( 'CNC', ['attribute'] );
		$attrItem = $product->getRefItems( 'attribute' )->first();

		$product = $this->object->save( $product->setId( null )->setCode( 'ModifiedCNC' ) );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrItem->getId() ) );
		$result = $this->object->search( $search )->toArray();


		$this->object->delete( $product->getId() );
		$productManager->delete( $product->getId() );


		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $attrItem->getId() ) );
		$result2 = $this->object->search( $search )->toArray();

		$this->assertTrue( in_array( $product->getId(), array_keys( $result ) ) );
		$this->assertFalse( in_array( $product->getId(), array_keys( $result2 ) ) );
	}


	public function testGetSubManager()
	{
		$this->expectException( \LogicException::class );
		$this->object->getSubManager( 'unknown' );
	}


	public function testIterate()
	{
		$attributeManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$id = $attributeManager->find( '30', [], 'product', 'length' )->getId();

		$filter = $this->object->filter()->add( 'index.attribute.id', '==', $id );

		$cursor = $this->object->cursor( $filter );
		$products = $this->object->iterate( $cursor );

		$this->assertEquals( 4, count( $products ) );

		foreach( $products as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testRemove()
	{
		$this->assertEquals( $this->object, $this->object->remove( [-1] ) );
	}


	public function testSearchItems()
	{
		$attributeManager = \Aimeos\MShop::create( $this->context, 'attribute' );
		$id = $attributeManager->find( '30', [], 'product', 'length' )->getId();

		$search = $this->object->filter();
		$search->setConditions( $search->compare( '==', 'index.attribute.id', $id ) );
		$result = $this->object->search( $search, [] );

		$this->assertEquals( 4, count( $result ) );
	}


	public function testSearchItemsNoId()
	{
		$search = $this->object->filter();
		$search->setConditions( $search->compare( '!=', 'index.attribute.id', null ) );
		$result = $this->object->search( $search, [] );

		$this->assertGreaterThanOrEqual( 2, count( $result ) );
	}


	public function testSearchItemsAllof()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$attrIds = [
			$manager->find( '30', [], 'product', 'length' )->getId(),
			$manager->find( '29', [], 'product', 'width' )->getId()
		];

		$search = $this->object->filter()->order( 'product.code' );

		$func = $search->make( 'index.attribute:allof', [$attrIds] );
		$search->setConditions( $search->compare( '!=', $func, null ) );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'CNE', $result->first()->getCode() );
	}


	public function testSearchItemsAllofArticle()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$attrIds = [
			$manager->find( '30', [], 'product', 'length' )->getId(),
			$manager->find( '30', [], 'product', 'width' )->getId()
		];

		$search = $this->object->filter()->order( 'product.code' );

		$func = $search->make( 'index.attribute:allof', [$attrIds] );
		$search->add( $func, '!=', null );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'U:TEST', $result->first()->getCode() );
	}


	public function testSearchItemsOneof()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$length = $manager->find( '30', [], 'product', 'length' )->getId();
		$width = $manager->find( '29', [], 'product', 'width' )->getId();

		$search = $this->object->filter()->order( 'product.code' );
		$search->add( [$search->make( 'index.attribute:oneof', [$length, $width] ) => null], '!=' );

		$result = $this->object->search( $search );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( 'CNE', $result->first()->getCode() );
	}


	public function testSearchItemsOneofArticle()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'attribute' );

		$color = $manager->find( 'white', [], 'product', 'color' )->getId();
		$size = $manager->find( 'm', [], 'product', 'size' )->getId();

		$search = $this->object->filter()->order( 'product.code' );
		$search->add( [
			$search->make( 'index.attribute:oneof', [$color] ) => null,
			$search->make( 'index.attribute:oneof', [$size] ) => null
		], '!=' );

		$result = $this->object->search( $search, [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 'U:TEST', $result->first()->getCode() );
	}


	public function testCleanup()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Index\Manager\Iface::class, $this->object->cleanup( '1970-01-01 00:00:00' ) );
	}

}

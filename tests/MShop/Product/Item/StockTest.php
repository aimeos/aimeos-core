<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2025
 */


namespace Aimeos\MShop\Product\Item;


class StockTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $stockItem;
	private $stockItem2;


	protected function setUp() : void
	{
		$this->stockItem = new \Aimeos\MShop\Stock\Item\Standard( 'stock.', ['stock.type' => 'test', 'stock.stocklevel' => 100] );
		$this->stockItem2 = new \Aimeos\MShop\Stock\Item\Standard( 'stock.', ['stock.type' => 'test2'] );

		$this->object = new \Aimeos\MShop\Product\Item\Standard( 'product.', ['product.id' => 'id'] );
		$this->object->addStockItem( $this->stockItem );
		$this->object->addStockItem( $this->stockItem2 );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->stockItem, $this->stockItem2 );
	}


	public function testAddStockItem()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( 'product.', ['product.id' => 'id'] );
		$result = $object->addStockItem( $this->stockItem );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $result );
		$this->assertEquals( ['_id_test' => $this->stockItem], $object->getStockItems()->toArray() );
	}


	public function testAddStockItems()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( 'product.', ['product.id' => 'id'] );
		$result = $object->addStockItems( [$this->stockItem, $this->stockItem2] );
		$expected = ['_id_test' => $this->stockItem, '_id_test2' => $this->stockItem2];

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $result );
		$this->assertEquals( $expected, $object->getStockItems( null, false )->toArray() );
	}


	public function testDeleteStockItem()
	{
		$this->object->deleteStockItem( $this->stockItem->setId( 123 ) );

		$this->assertEquals( ['_id_test2' => $this->stockItem2], $this->object->getStockItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test' => $this->stockItem], $this->object->getStockItemsDeleted()->toArray() );
	}


	public function testDeleteStockItems()
	{
		$this->object->deleteStockItems( [$this->stockItem] );

		$this->assertEquals( ['_id_test2' => $this->stockItem2], $this->object->getStockItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test' => $this->stockItem], $this->object->getStockItemsDeleted()->toArray() );
	}


	public function testGetStockItems()
	{
		$object = new \Aimeos\MShop\Product\Item\Standard( 'product.', ['.stock' => []] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getStockItems() );
		$this->assertEquals( [], $object->getStockItems()->toArray() );
	}


	public function testGetStockItemsWithType()
	{
		$stock = new \Aimeos\MShop\Stock\Item\Standard( 'stock.', [] );
		$stocks = [123 => ( clone $stock )->setType( 'something' ), 456 => ( clone $stock )->setType( 'default' )];
		$object = new \Aimeos\MShop\Product\Item\Standard( 'product.', ['.stock' => $stocks] );

		$this->assertInstanceOf( \Aimeos\Map::class, $object->getStockItems( 'default' ) );
		$this->assertEquals( 'default', $object->getStockItems( 'default' )->getType()->first() );
		$this->assertCount( 1, $object->getStockItems( 'default' ) );
	}


	public function testGetStockItemsWithTypes()
	{
		$expected = [$this->stockItem, $this->stockItem2];
		$this->assertEquals( $expected, $this->object->getStockItems( ['test', 'test2'], false )->values()->toArray() );
	}


	public function testGetStockItemsDeleted()
	{
		$this->assertEquals( [], $this->object->getStockItemsDeleted()->toArray() );
	}


	public function testSetStockItems()
	{
		$expected = ['_id_test2' => $this->stockItem2, '_id_test' => $this->stockItem];
		$result = $this->object->setStockItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $result );
		$this->assertEquals( $expected, $this->object->getStockItems( null, false )->toArray() );
	}


	public function testSetStockItemsRemove()
	{
		$expected = ['_id_test2' => $this->stockItem2];
		$result = $this->object->setStockItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Product\Item\Iface', $result );
		$this->assertEquals( $expected, $this->object->getStockItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test' => $this->stockItem], $this->object->getStockItemsDeleted()->toArray() );
	}
}

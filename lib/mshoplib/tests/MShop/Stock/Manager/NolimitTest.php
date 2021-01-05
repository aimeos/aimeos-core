<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MShop\Stock\Manager;


class NolimitTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Stock\Manager\Nolimit( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testClear()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->clear( [-1] ) );
	}


	public function testDeleteItems()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( [-1] ) );
	}


	public function testGetItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $this->object->get( -1 ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->get( -1 );
		$item = $this->object->save( $item );

		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->delete( $item->getId() ) );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->filter()->slice( 0, 2 );
		$search->setConditions( $search->compare( '==', 'stock.productid', ['123', '456', '789'] ) );
		$results = $this->object->search( $search, [], $total )->toArray();

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testDecrease()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->decrease( ['text' => 5] ) );
	}


	public function testIncrease()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Common\Manager\Iface::class, $this->object->increase( ['text' => 5] ) );
	}
}

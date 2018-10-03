<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Stock\Manager;


class NolimitTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Stock\Manager\Nolimit( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testFindItem()
	{
		$item = $this->object->findItem( 'CNC', [], 'product', 'default' );

		$this->assertEquals( 'CNC', $item->getProductCode() );
	}


	public function testGetItem()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Stock\\Item\\Iface', $this->object->getItem( -1 ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->object->getItem( -1 );
		$item = $this->object->saveItem( $item );
		$this->object->deleteItem( $item->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr[] = $search->compare( '==', 'stock.productcode', ['abc', 'def', 'ghi'] );

		$search->setConditions( $search->compare( '==', 'stock.productcode', ['abc', 'def', 'ghi'] ) );
		$search->setSlice( 0, 2 );
		$results = $this->object->searchItems( $search, [], $total );

		$this->assertEquals( 2, count( $results ) );
		$this->assertEquals( 3, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testIncrease()
	{
		$this->object->increase( 'text', 'default', 5 );
	}
}

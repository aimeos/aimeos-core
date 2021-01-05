<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


class TraitsClass extends \Aimeos\MShop\Common\Item\Base implements Iface
{
	use \Aimeos\MShop\Common\Item\PropertyRef\Traits;

	public function getId() : ?string
	{
		return 'id';
	}

	public function getResourceType() : string
	{
		return 'product';
	}
}


class TraitsTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $propItem;
	private $propItem2;


	protected function setUp() : void
	{
		$this->propItem = new \Aimeos\MShop\Common\Item\Property\Standard( 'c.', ['.languageid' => 'de', 'c.type' => 'test', 'c.value' => 'value'] );
		$this->propItem2 = new \Aimeos\MShop\Common\Item\Property\Standard( 'c.', ['.languageid' => 'de', 'c.languageid' => 'en', 'c.type' => 'test2'] );

		$this->object = new TraitsClass( 'product.', [] );
		$this->object->addPropertyItem( $this->propItem );
		$this->object->addPropertyItem( $this->propItem2 );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->propItem, $this->propItem2 );
	}


	public function testAddPropertyItem()
	{
		$object = new TraitsClass( 'product.', [] );
		$result = $object->addPropertyItem( $this->propItem );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $object->getPropertyItems()->toArray() );
	}


	public function testAddPropertyItems()
	{
		$object = new TraitsClass( 'product.', [] );
		$result = $object->addPropertyItems( [$this->propItem, $this->propItem2] );
		$expected = ['_id_test__value' => $this->propItem, '_id_test2_en_' => $this->propItem2];

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $object->getPropertyItems( null, false )->toArray() );
	}


	public function testDeletePropertyItem()
	{
		$this->object->deletePropertyItem( $this->propItem->setId( 123 ) );

		$this->assertEquals( ['_id_test2_en_' => $this->propItem2], $this->object->getPropertyItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted()->toArray() );
	}


	public function testDeletePropertyItems()
	{
		$this->object->deletePropertyItems( [$this->propItem] );

		$this->assertEquals( ['_id_test2_en_' => $this->propItem2], $this->object->getPropertyItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted()->toArray() );
	}


	public function testGetProperties()
	{
		$this->assertEquals( ['_id_test__value' => 'value'], $this->object->getProperties( 'test' )->toArray() );
	}


	public function testGetPropertyItem()
	{
		$this->assertEquals( $this->propItem, $this->object->getPropertyItem( 'test', null, 'value', false ) );
	}


	public function testGetPropertyItems()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false )->values()->toArray() );
	}


	public function testGetPropertyItemsActive()
	{
		$this->assertEquals( [$this->propItem], $this->object->getPropertyItems()->values()->toArray() );
	}


	public function testGetPropertyItemsWithType()
	{
		$this->assertEquals( [$this->propItem2], $this->object->getPropertyItems( 'test2', false )->values()->toArray() );
	}


	public function testGetPropertyItemsWithTypes()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, $this->object->getPropertyItems( ['test', 'test2'], false )->values()->toArray() );
	}


	public function testGetPropertyItemsDeleted()
	{
		$this->assertEquals( [], $this->object->getPropertyItemsDeleted()->toArray() );
	}


	public function testSetPropertyItems()
	{
		$expected = ['_id_test2_en_' => $this->propItem2, '_id_test__value' => $this->propItem];
		$result = $this->object->setPropertyItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false )->toArray() );
	}


	public function testSetPropertyItemsRemove()
	{
		$expected = ['_id_test2_en_' => $this->propItem2];
		$result = $this->object->setPropertyItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false )->toArray() );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted()->toArray() );
	}
}

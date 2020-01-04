<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
		$this->assertEquals( ['_id_test__value' => $this->propItem], $object->getPropertyItems() );
	}


	public function testAddPropertyItems()
	{
		$object = new TraitsClass( 'product.', [] );
		$result = $object->addPropertyItems( [$this->propItem, $this->propItem2] );
		$expected = ['_id_test__value' => $this->propItem, '_id_test2_en_' => $this->propItem2];

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $object->getPropertyItems( null, false ) );
	}


	public function testDeletePropertyItem()
	{
		$this->object->deletePropertyItem( $this->propItem->setId( 123 ) );

		$this->assertEquals( ['_id_test2_en_' => $this->propItem2], $this->object->getPropertyItems( null, false ) );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted() );
	}


	public function testDeletePropertyItems()
	{
		$this->object->deletePropertyItems( [$this->propItem] );

		$this->assertEquals( ['_id_test2_en_' => $this->propItem2], $this->object->getPropertyItems( null, false ) );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted() );
	}


	public function testGetProperties()
	{
		$this->assertEquals( ['value'], array_values( $this->object->getProperties( 'test' ) ) );
	}


	public function testGetPropertyItem()
	{
		$this->assertEquals( $this->propItem, $this->object->getPropertyItem( 'test', null, 'value', false ) );
	}


	public function testGetPropertyItems()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, array_values( $this->object->getPropertyItems( null, false ) ) );
	}


	public function testGetPropertyItemsActive()
	{
		$this->assertEquals( [$this->propItem], array_values( $this->object->getPropertyItems() ) );
	}


	public function testGetPropertyItemsWithType()
	{
		$this->assertEquals( [$this->propItem2], array_values( $this->object->getPropertyItems( 'test2', false ) ) );
	}


	public function testGetPropertyItemsWithTypes()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, array_values( $this->object->getPropertyItems( ['test', 'test2'], false ) ) );
	}


	public function testGetPropertyItemsDeleted()
	{
		$this->assertEquals( [], $this->object->getPropertyItemsDeleted() );
	}


	public function testSetPropertyItems()
	{
		$expected = ['_id_test2_en_' => $this->propItem2, '_id_test__value' => $this->propItem];
		$result = $this->object->setPropertyItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false ) );
	}


	public function testSetPropertyItemsRemove()
	{
		$expected = ['_id_test2_en_' => $this->propItem2];
		$result = $this->object->setPropertyItems( $expected );

		$this->assertInstanceOf( '\Aimeos\MShop\Common\Item\PropertyRef\Iface', $result );
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false ) );
		$this->assertEquals( ['_id_test__value' => $this->propItem], $this->object->getPropertyItemsDeleted() );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


class TraitsClass
{
	use \Aimeos\MShop\Common\Item\PropertyRef\Traits;

	public function setPropertyItems( $list )
	{
		$this->propItems = $list;
	}
}


class TraitsTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $propItem;
	private $propItem2;


	protected function setUp()
	{
		$this->propItem = new \Aimeos\MShop\Common\Item\Property\Standard( 'c.', ['languageid' => 'de', 'c.type' => 'test', 'c.value' => 'value']);
		$this->propItem2 = new \Aimeos\MShop\Common\Item\Property\Standard( 'c.', ['languageid' => 'de', 'c.languageid' => 'en', 'c.type' => 'test2']);

		$this->object = new TraitsClass();
		$this->object->setPropertyItems( [$this->propItem, $this->propItem2] );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->propItem, $this->propItem2 );
	}


	public function testGetProperties()
	{
		$this->assertEquals( ['value'], $this->object->getProperties( 'test' ) );
	}


	public function testGetPropertyItems()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, $this->object->getPropertyItems( null, false ) );
	}


	public function testGetPropertyItemsActive()
	{
		$this->assertEquals( [$this->propItem], $this->object->getPropertyItems() );
	}


	public function testGetPropertyItemsWithType()
	{
		$this->assertEquals( [1 => $this->propItem2], $this->object->getPropertyItems( 'test2', false ) );
	}


	public function testGetPropertyItemsWithTypes()
	{
		$expected = [$this->propItem, $this->propItem2];
		$this->assertEquals( $expected, $this->object->getPropertyItems( ['test', 'test2'], false ) );
	}


	public function testGetPropertyItemsDeleted()
	{
		$this->assertEquals( [], $this->object->getPropertyItemsDeleted() );
	}


	public function testAddPropertyItem()
	{
		$object = new TraitsClass();
		$object->addPropertyItem( $this->propItem );

		$this->assertEquals( ['id-0' => $this->propItem], $object->getPropertyItems() );
	}


	public function testDeletePropertyItem()
	{
		$this->object->deletePropertyItem( $this->propItem->setId( 123 ) );

		$this->assertEquals( [1 => $this->propItem2], $this->object->getPropertyItems( null, false ) );
		$this->assertEquals( [123 => $this->propItem], $this->object->getPropertyItemsDeleted() );
	}


	public function testDeletePropertyItemException()
	{
		$object = new TraitsClass();

		$this->setExpectedException( '\Aimeos\MShop\Exception' );
		$object->deletePropertyItem( $this->propItem );
	}
}

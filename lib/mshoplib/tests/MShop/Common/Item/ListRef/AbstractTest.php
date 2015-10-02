<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class MShop_Common_Item_ListRef_Test extends MShop_Common_Item_ListRef_Abstract
{
	function getLabel()
	{
		return 'test label';
	}
}


/**
 * Test class for MShop_Common_Item_ListRef_Abstract
 */
class MShop_Common_Item_ListRef_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $textItem1;
	private $textItem2;
	private $listItem1;
	private $listItem2;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->textItem1 = new MShop_Text_Item_Default( array( 'type' => 'name' ) );
		$this->textItem1->setContent( 'test name' );
		$this->textItem1->setId( 1 );

		$this->textItem2 = new MShop_Text_Item_Default( array( 'type' => 'name' ) );
		$this->textItem2->setContent( 'default name' );
		$this->textItem2->setId( 2 );

		$this->listItem1 = new MShop_Common_Item_List_Default( 'test', array( 'type' => 'test' ) );
		$this->listItem1->setRefId( $this->textItem1->getId() );
		$this->listItem1->setPosition( 1 );
		$this->listItem1->setId( 11 );

		$this->listItem2 = new MShop_Common_Item_List_Default( 'test', array( 'type' => 'default' ) );
		$this->listItem2->setRefId( $this->textItem2->getId() );
		$this->listItem2->setPosition( 0 );
		$this->listItem2->setId( 10 );

		$listItems = array( 'text' => array(
			$this->listItem1->getId() => $this->listItem1,
			$this->listItem2->getId() => $this->listItem2,
		) );

		$refItems = array( 'text' => array(
			$this->textItem1->getId() => $this->textItem1,
			$this->textItem2->getId() => $this->textItem2,
		) );

		$this->object = new MShop_Common_Item_ListRef_Test( '', array(), $listItems, $refItems );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetName()
	{
		$object = new MShop_Common_Item_ListRef_Test( '' );

		$this->assertEquals( $object->getName(), 'test label' );
		$this->assertEquals( $this->object->getName(), 'default name' );
	}


	public function testGetListItems()
	{
		$result = $this->object->getListItems();
		$expected = array(
			$this->listItem2->getId() => $this->listItem2,
			$this->listItem1->getId() => $this->listItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $listItem ) {
			$this->assertInstanceof( 'MShop_Common_Item_List_Interface', $listItem );
		}
	}


	public function testGetListItemsWithDomain()
	{
		$result = $this->object->getListItems( 'text' );
		$expected = array(
			$this->listItem2->getId() => $this->listItem2,
			$this->listItem1->getId() => $this->listItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $listItem ) {
			$this->assertInstanceof( 'MShop_Common_Item_List_Interface', $listItem );
		}

		$this->assertEquals( array(), $this->object->getListItems( 'undefined' ) );
	}


	public function testGetListItemsWithType()
	{
		$result = $this->object->getListItems( 'text', 'test' );
		$expected = array( $this->listItem1->getId() => $this->listItem1 );

		$this->assertEquals( $expected, $result );
	}


	public function testGetListItemsWithTypes()
	{
		$result = $this->object->getListItems( 'text', array( 'test' ) );
		$expected = array( $this->listItem1->getId() => $this->listItem1 );

		$this->assertEquals( $expected, $result );
	}


	public function testGetListItemsWithRefItems()
	{
		$result = $this->object->getListItems( 'text' );
		$expected = array(
			$this->textItem2->getId() => $this->textItem2,
			$this->textItem1->getId() => $this->textItem1,
		);

		foreach( $result as $listItem )
		{
			$this->assertInstanceof( 'MShop_Text_Item_Interface', $listItem->getRefItem() );
			$this->assertSame( $expected[$listItem->getRefId()], $listItem->getRefItem() );
		}
	}


	public function testGetRefItems()
	{
		$result = $this->object->getRefItems( 'text' );
		$expected = array(
			$this->textItem2->getId() => $this->textItem2,
			$this->textItem1->getId() => $this->textItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $item ) {
			$this->assertInstanceof( 'MShop_Common_Item_Interface', $item );
		}

		$this->assertEquals( array(), $this->object->getRefItems( 'undefined' ) );
	}


	public function testGetRefItemsWithType()
	{
		$result = $this->object->getRefItems( 'text', 'name' );
		$expected = array(
			$this->textItem2->getId() => $this->textItem2,
			$this->textItem1->getId() => $this->textItem1,
		);

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->object->getRefItems( 'text', 'undefined' ) );
	}


	public function testGetRefItemsWithTypes()
	{
		$result = $this->object->getRefItems( 'text', array( 'name' ) );
		$expected = array(
				$this->textItem2->getId() => $this->textItem2,
				$this->textItem1->getId() => $this->textItem1,
		);

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->object->getRefItems( 'text', 'undefined' ) );
	}


	public function testGetRefItemsWithTypeAndListtype()
	{
		$result = $this->object->getRefItems( 'text', 'name', 'test' );
		$expected = array( $this->textItem1->getId() => $this->textItem1 );

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->object->getRefItems( 'text', 'name', 'undefined' ) );
	}
}

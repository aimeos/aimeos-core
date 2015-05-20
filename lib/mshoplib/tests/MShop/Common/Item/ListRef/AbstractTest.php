<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
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
class MShop_Common_Item_ListRef_AbstractTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_textItem1;
	private $_textItem2;
	private $_listItem1;
	private $_listItem2;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_textItem1 = new MShop_Text_Item_Default( array( 'type' => 'name' ) );
		$this->_textItem1->setContent( 'test name' );
		$this->_textItem1->setId( 1 );

		$this->_textItem2 = new MShop_Text_Item_Default( array( 'type' => 'name' ) );
		$this->_textItem2->setContent( 'default name' );
		$this->_textItem2->setId( 2 );

		$this->_listItem1 = new MShop_Common_Item_List_Default( 'test', array( 'type' => 'test' ) );
		$this->_listItem1->setRefId( $this->_textItem1->getId() );
		$this->_listItem1->setPosition( 1 );
		$this->_listItem1->setId( 11 );

		$this->_listItem2 = new MShop_Common_Item_List_Default( 'test', array( 'type' => 'default' ) );
		$this->_listItem2->setRefId( $this->_textItem2->getId() );
		$this->_listItem2->setPosition( 0 );
		$this->_listItem2->setId( 10 );

		$listItems = array( 'text' => array(
			$this->_listItem1->getId() => $this->_listItem1,
			$this->_listItem2->getId() => $this->_listItem2,
		) );

		$refItems = array( 'text' => array(
			$this->_textItem1->getId() => $this->_textItem1,
			$this->_textItem2->getId() => $this->_textItem2,
		) );

		$this->_object = new MShop_Common_Item_ListRef_Test( '', array(), $listItems, $refItems );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testGetName()
	{
		$object = new MShop_Common_Item_ListRef_Test( '' );

		$this->assertEquals( $object->getName(), 'test label' );
		$this->assertEquals( $this->_object->getName(), 'default name' );
	}


	public function testGetListItems()
	{
		$result = $this->_object->getListItems();
		$expected = array(
			$this->_listItem2->getId() => $this->_listItem2,
			$this->_listItem1->getId() => $this->_listItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $listItem ) {
			$this->assertInstanceof( 'MShop_Common_Item_List_Interface', $listItem );
		}
	}


	public function testGetListItemsWithDomain()
	{
		$result = $this->_object->getListItems( 'text' );
		$expected = array(
			$this->_listItem2->getId() => $this->_listItem2,
			$this->_listItem1->getId() => $this->_listItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $listItem ) {
			$this->assertInstanceof( 'MShop_Common_Item_List_Interface', $listItem );
		}

		$this->assertEquals( array(), $this->_object->getListItems( 'undefined' ) );
	}


	public function testGetListItemsWithType()
	{
		$result = $this->_object->getListItems( 'text', 'test' );
		$expected = array( $this->_listItem1->getId() => $this->_listItem1 );

		$this->assertEquals( $expected, $result );
	}


	public function testGetListItemsWithTypes()
	{
		$result = $this->_object->getListItems( 'text', array( 'test' ) );
		$expected = array( $this->_listItem1->getId() => $this->_listItem1 );

		$this->assertEquals( $expected, $result );
	}


	public function testGetListItemsWithRefItems()
	{
		$result = $this->_object->getListItems( 'text' );
		$expected = array(
			$this->_textItem2->getId() => $this->_textItem2,
			$this->_textItem1->getId() => $this->_textItem1,
		);

		foreach( $result as $listItem )
		{
			$this->assertInstanceof( 'MShop_Text_Item_Interface', $listItem->getRefItem() );
			$this->assertSame( $expected[ $listItem->getRefId() ], $listItem->getRefItem() );
		}
	}


	public function testGetRefItems()
	{
		$result = $this->_object->getRefItems( 'text' );
		$expected = array(
			$this->_textItem2->getId() => $this->_textItem2,
			$this->_textItem1->getId() => $this->_textItem1,
		);

		$this->assertEquals( $expected, $result );

		foreach( $result as $item ) {
			$this->assertInstanceof( 'MShop_Common_Item_Interface', $item );
		}

		$this->assertEquals( array(), $this->_object->getRefItems( 'undefined' ) );
	}


	public function testGetRefItemsWithType()
	{
		$result = $this->_object->getRefItems( 'text', 'name' );
		$expected = array(
			$this->_textItem2->getId() => $this->_textItem2,
			$this->_textItem1->getId() => $this->_textItem1,
		);

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->_object->getRefItems( 'text', 'undefined' ) );
	}


	public function testGetRefItemsWithTypes()
	{
		$result = $this->_object->getRefItems( 'text', array( 'name' ) );
		$expected = array(
				$this->_textItem2->getId() => $this->_textItem2,
				$this->_textItem1->getId() => $this->_textItem1,
		);

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->_object->getRefItems( 'text', 'undefined' ) );
	}


	public function testGetRefItemsWithTypeAndListtype()
	{
		$result = $this->_object->getRefItems( 'text', 'name', 'test' );
		$expected = array( $this->_textItem1->getId() => $this->_textItem1 );

		$this->assertEquals( $expected, $result );
		$this->assertEquals( array(), $this->_object->getRefItems( 'text', 'name', 'undefined' ) );
	}
}

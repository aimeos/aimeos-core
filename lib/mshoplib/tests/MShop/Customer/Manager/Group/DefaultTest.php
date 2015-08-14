<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MShop_Common_Manager_Group_AbstractTest extends PHPUnit_Framework_TestCase
{
	private $_object = null;
	private $_editor = '';


	protected function setUp()
	{
		$context = TestHelper::getContext();
		$this->_editor = $context->getEditor();

		$this->_object = new MShop_Customer_Manager_Group_Default( $context );
	}


	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Customer_Item_Group_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.group.label', 'Unitgroup' ) );

		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No group item with label "Unitgroup" found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$item->setCode( 'unittest-group' );
		$item->setLabel( 'unittest group' );

		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'unittest 2. group' );

		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}


	public function testSearchItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'customer.group.code', 'unitgroup' ),
			$search->compare( '==', 'customer.group.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );

		$total = 0;
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );
	}

}

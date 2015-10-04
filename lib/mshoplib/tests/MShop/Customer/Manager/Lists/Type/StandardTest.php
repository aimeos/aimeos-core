<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Customer_Manager_Lists_Type_Standard.
 */
class MShop_Customer_Manager_Lists_Type_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$manager = MShop_Customer_Manager_Factory::createManager( TestHelper::getContext(), 'Standard' );

		$listManager = $manager->getSubManager( 'lists', 'Standard' );
		$this->object = $listManager->getSubManager( 'type', 'Standard' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_Type_Iface', $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No list type item found' );
		}

		$this->assertEquals( $expected, $this->object->getItem( $expected->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$search->setSlice( 0, 1 );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestInit' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'customer.list.type.id', 0 );
		$expr[] = $search->compare( '!=', 'customer.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'customer.list.type.code', 'default' );
		$expr[] = $search->compare( '==', 'customer.list.type.domain', 'text' );
		$expr[] = $search->compare( '==', 'customer.list.type.label', 'Standard' );
		$expr[] = $search->compare( '==', 'customer.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'customer.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'customer.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'customer.list.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

}

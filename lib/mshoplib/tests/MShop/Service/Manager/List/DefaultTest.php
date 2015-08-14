<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Service_Manager_List_Default.
 */
class MShop_Service_Manager_List_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_editor = $this->_context->getEditor();
		$serviceManager = MShop_Service_Manager_Factory::createManager( $this->_context, 'Default' );
		$this->_object = $serviceManager->getSubManager( 'list', 'Default' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_context );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testAggregate()
	{
		$search = $this->_object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'service.list.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->aggregate( $search, 'service.list.domain' );

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'text', $result );
		$this->assertEquals( 7, $result['text'] );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'service.list.editor', $this->_editor ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'service.list.editor', $this->_editor ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getRefId(), $itemSaved->getRefId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getRefId(), $itemUpd->getRefId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testMoveItemLastToFront()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first service list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last service list item' );
		}

		$this->_object->moveItem( $last->getId(), $first->getId() );

		$newFirst = $this->_object->getItem( $last->getId() );
		$newSecond = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $last->getId() );

		$this->assertEquals( 3, $newFirst->getPosition() );
		$this->assertEquals( 4, $newSecond->getPosition() );
	}


	public function testMoveItemFirstToLast()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first service list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second service list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last service list item' );
		}

		$this->_object->moveItem( $first->getId() );

		$newBefore = $this->_object->getItem( $last->getId() );
		$newLast = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newBefore->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testMoveItemFirstUp()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first service list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second service list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last service list item' );
		}

		$this->_object->moveItem( $first->getId(), $last->getId() );

		$newLast = $this->_object->getItem( $last->getId() );
		$newUp = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newUp->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'service.list.id', null );
		$expr[] = $search->compare( '!=', 'service.list.siteid', null );
		$expr[] = $search->compare( '>', 'service.list.parentid', 0 );
		$expr[] = $search->compare( '==', 'service.list.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.list.typeid', 0 );
		$expr[] = $search->compare( '>', 'service.list.refid', 0 );
		$expr[] = $search->compare( '==', 'service.list.datestart', null );
		$expr[] = $search->compare( '==', 'service.list.dateend', null );
		$expr[] = $search->compare( '!=', 'service.list.config', null );
		$expr[] = $search->compare( '==', 'service.list.position', 0 );
		$expr[] = $search->compare( '==', 'service.list.status', 1 );
		$expr[] = $search->compare( '>=', 'service.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'service.list.type.id', null );
		$expr[] = $search->compare( '!=', 'service.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.list.type.code', 'unittype1' );
		$expr[] = $search->compare( '==', 'service.list.type.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.list.type.label', '' );
		$expr[] = $search->compare( '==', 'service.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.list.type.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 2, count( $results ) );


		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'service.list.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 3);
		$results = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals( 3, count( $results ) );
		$this->assertEquals( 12, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	protected function _getListItems()
	{
		$manager = MShop_Service_Manager_Factory::createManager( $this->_context, 'Default' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'service.code', 'unitcode' ),
			$search->compare( '==', 'service.type.code', 'delivery' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'service.list.parentid', $item->getId() ),
			$search->compare( '==', 'service.list.domain', 'text' ),
			$search->compare( '==', 'service.list.editor', $this->_editor ),
			$search->compare( '==', 'service.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'service.list.position' ) ) );

		return $this->_object->searchItems( $search );
	}
}

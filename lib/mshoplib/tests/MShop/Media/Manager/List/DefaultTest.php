<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Media_Manager_List_Default.
 */
class MShop_Media_Manager_List_DefaultTest extends MW_Unittest_Testcase
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
		$manager = MShop_Media_Manager_Factory::createManager( $this->_context, 'Default' );
		$this->_object = $manager->getSubManager( 'list', 'Default');
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
			$search->compare( '==', 'media.list.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->_object->aggregate( $search, 'media.list.domain' );

		$this->assertEquals( 2, count( $result ) );
		$this->assertArrayHasKey( 'attribute', $result );
		$this->assertEquals( 6, $result['attribute'] );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId(null);
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

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testMoveItemLastToFront()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last media list item' );
		}

		$this->_object->moveItem( $last->getId(), $first->getId() );

		$newFirst = $this->_object->getItem( $last->getId() );
		$newSecond = $this->_object->getItem( $first->getId() );

		$this->_object->moveItem( $last->getId() );

		$this->assertEquals( 0, $newFirst->getPosition() );
		$this->assertEquals( 1, $newSecond->getPosition() );
	}


	public function testMoveItemFirstToLast()
	{
		$listItems = $this->_getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first media list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last media list item' );
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
			throw new Exception( 'No first media list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second media list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last media list item' );
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
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'media.list.id', null );
		$expr[] = $search->compare( '!=', 'media.list.siteid', null );
		$expr[] = $search->compare( '>', 'media.list.parentid', 0 );
		$expr[] = $search->compare( '==', 'media.list.domain', 'text' );
		$expr[] = $search->compare( '>', 'media.list.typeid', 0 );
		$expr[] = $search->compare( '>', 'media.list.refid', 0 );
		$expr[] = $search->compare( '==', 'media.list.datestart', null );
		$expr[] = $search->compare( '==', 'media.list.dateend', null );
		$expr[] = $search->compare( '!=', 'media.list.config', null );
		$expr[] = $search->compare( '==', 'media.list.position', 0 );
		$expr[] = $search->compare( '==', 'media.list.status', 1 );
		$expr[] = $search->compare( '>=', 'media.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'media.list.type.id', null );
		$expr[] = $search->compare( '!=', 'media.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'media.list.type.code', 'option' );
		$expr[] = $search->compare( '==', 'media.list.type.domain', 'attribute' );
		$expr[] = $search->compare( '>', 'media.list.type.label', '' );
		$expr[] = $search->compare( '==', 'media.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'media.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.list.type.editor', $this->_editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'media.list.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 5);
		$results = $this->_object->searchItems( $search, array(), $total);
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 7, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	protected function _getListItems()
	{
		$manager = MShop_Media_Manager_Factory::createManager( $this->_context, 'Default' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No media item found' );
		}

		$search = $this->_object->createSearch();
		$expr = array(
			$search->compare( '==', 'media.list.parentid', $item->getId() ),
			$search->compare( '==', 'media.list.domain', 'attribute' ),
			$search->compare( '==', 'media.list.editor', $this->_editor ),
			$search->compare( '==', 'media.list.type.code', 'option' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'media.list.position' ) ) );

		return $this->_object->searchItems( $search );
	}
}

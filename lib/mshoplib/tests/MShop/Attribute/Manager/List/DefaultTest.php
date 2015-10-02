<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Attribute_Manager_List_Default.
 */
class MShop_Attribute_Manager_List_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->context = TestHelper::getContext();
		$this->editor = $this->context->getEditor();
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->context, 'Default' );
		$this->object = $manager->getSubManager( 'list', 'Default' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testAggregate()
	{
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'attribute.list.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$result = $this->object->aggregate( $search, 'attribute.list.domain' );

		$this->assertEquals( 3, count( $result ) );
		$this->assertArrayHasKey( 'text', $result );
		$this->assertEquals( 24, $result['text'] );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_List_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$item->setId( null );
		$item->setDomain( 'unittest' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'unittest2' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );

		$context = TestHelper::getContext();

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

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
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

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testMoveItemLastToFront()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first attribute list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last attribute list item' );
		}

		$this->object->moveItem( $last->getId(), $first->getId() );

		$newFirst = $this->object->getItem( $last->getId() );
		$newSecond = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $last->getId() );

		$this->assertEquals( 0, $newFirst->getPosition() );
		$this->assertEquals( 1, $newSecond->getPosition() );
	}


	public function testMoveItemFirstToLast()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first attribute list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second attribute list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last attribute list item' );
		}

		$this->object->moveItem( $first->getId() );

		$newBefore = $this->object->getItem( $last->getId() );
		$newLast = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newBefore->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testMoveItemFirstUp()
	{
		$listItems = $this->getListItems();
		$this->assertGreaterThan( 1, count( $listItems ) );

		if( ( $first = reset( $listItems ) ) === false ) {
			throw new Exception( 'No first attribute list item' );
		}

		if( ( $second = next( $listItems ) ) === false ) {
			throw new Exception( 'No second attribute list item' );
		}

		if( ( $last = end( $listItems ) ) === false ) {
			throw new Exception( 'No last attribute list item' );
		}

		$this->object->moveItem( $first->getId(), $last->getId() );

		$newLast = $this->object->getItem( $last->getId() );
		$newUp = $this->object->getItem( $first->getId() );

		$this->object->moveItem( $first->getId(), $second->getId() );

		$this->assertEquals( $last->getPosition() - 1, $newUp->getPosition() );
		$this->assertEquals( $last->getPosition(), $newLast->getPosition() );
	}


	public function testSearchItems()
	{
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'attribute.list.id', null );
		$expr[] = $search->compare( '!=', 'attribute.list.siteid', null );
		$expr[] = $search->compare( '!=', 'attribute.list.parentid', null );
		$expr[] = $search->compare( '==', 'attribute.list.domain', 'text' );
		$expr[] = $search->compare( '!=', 'attribute.list.typeid', null );
		$expr[] = $search->compare( '>', 'attribute.list.refid', 0 );
		$expr[] = $search->compare( '==', 'attribute.list.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.list.dateend', '2001-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'attribute.list.config', null );
		$expr[] = $search->compare( '==', 'attribute.list.position', 0 );
		$expr[] = $search->compare( '==', 'attribute.list.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.list.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'attribute.list.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.list.type.code', 'default' );
		$expr[] = $search->compare( '==', 'attribute.list.type.domain', 'text' );
		$expr[] = $search->compare( '==', 'attribute.list.type.label', 'Default' );
		$expr[] = $search->compare( '==', 'attribute.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.list.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );


		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'attribute.list.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->object->searchItems( $search );
		$this->assertEquals( 26, count( $results ) );
		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		// search without base criteria, slice & total
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.list.domain', 'text' ),
			$search->compare( '==', 'attribute.list.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$items = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 25, $total );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type', 'default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	protected function getListItems()
	{
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->context, 'Default' );

		$search = $manager->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.code', 'xs' ),
			$search->compare( '==', 'attribute.domain', 'product' ),
			$search->compare( '==', 'attribute.editor', $this->editor ),
			$search->compare( '==', 'attribute.type.code', 'size' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 1 );

		$results = $manager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}

		$search = $this->object->createSearch();
		$expr = array(
			$search->compare( '==', 'attribute.list.parentid', $item->getId() ),
			$search->compare( '==', 'attribute.list.domain', 'text' ),
			$search->compare( '==', 'attribute.list.editor', $this->editor ),
			$search->compare( '==', 'attribute.list.type.code', 'default' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSortations( array( $search->sort( '+', 'attribute.list.position' ) ) );

		return $this->object->searchItems( $search );
	}
}

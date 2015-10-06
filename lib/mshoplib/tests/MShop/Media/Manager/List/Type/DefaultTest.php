<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Media_Manager_List_Type_Default.
 */
class MShop_Media_Manager_List_Type_DefaultTest extends PHPUnit_Framework_TestCase
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
		$manager = MShop_Media_Manager_Factory::createManager( TestHelper::getContext() );

		$listManager = $manager->getSubManager( 'list' );
		$this->object = $listManager->getSubManager( 'type' );
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
		$this->assertInstanceOf( 'MShop_Common_Item_Type_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$results = $this->object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No media list type item found' );
		}

		$this->assertEquals( $expected, $this->object->getItem( $expected->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = $search->compare( '==', 'media.list.type.editor', $this->editor );
		$search->setConditions( $conditions );
		$results = $this->object->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestSave' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
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
		//search without base criteria
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'media.list.type.id', null );
		$expr[] = $search->compare( '!=', 'media.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'media.list.type.code', 'option' );
		$expr[] = $search->compare( '==', 'media.list.type.domain', 'attribute' );
		$expr[] = $search->compare( '>', 'media.list.type.label', '' );
		$expr[] = $search->compare( '==', 'media.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'media.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.list.type.editor', $this->editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );


		// search with base critera
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'media.list.type.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 7 );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 7, count( $results ) );
		$this->assertEquals( 10, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}
}

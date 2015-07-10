<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Catalog_Manager_List_Type_Default.
 */
class MShop_Catalog_Manager_List_Type_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_editor = '';


	/**
	 * Sets up the fixture.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$attributeManager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );

		$attributeListManager = $attributeManager->getSubManager( 'list' );
		$this->_object = $attributeListManager->getSubManager( 'type' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$item = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Common_Item_Type_Interface', $item );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems($search);

		if( ( $expected = reset($results) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $expected, $this->_object->getItem( $expected->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$results = $this->_object->searchItems($search);

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$item->setId(null);
		$item->setCode( 'unitTestSave' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'catalog.list.type.id', null );
		$expr[] = $search->compare( '!=', 'catalog.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.list.type.code', 'default' );
		$expr[] = $search->compare( '==', 'catalog.list.type.domain', 'text' );
		$expr[] = $search->compare( '>', 'catalog.list.type.label', '' );
		$expr[] = $search->compare( '==', 'catalog.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'catalog.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.type.editor', $this->_editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );


		// search with base criteria
		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'catalog.list.type.domain', 'text' ),
			$search->compare( '==', 'catalog.list.type.code', 'unittype1' ),
			$search->compare( '==', 'catalog.list.type.editor', $this->_editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems($search);

		$this->assertEquals( 1, count( $results ) );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		// search without base criteria, slice & total
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'catalog.list.type.code', 'unit' ),
			$search->compare( '~=', 'catalog.list.type.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$items = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 27, $total );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}

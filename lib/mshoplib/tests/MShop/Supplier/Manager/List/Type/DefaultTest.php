<?php

/**
 * @copyright Aimeos (aimeos.org), 2015
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Supplier_Manager_List_Type_Default.
 */
class MShop_Supplier_Manager_List_Type_DefaultTest extends MW_Unittest_Testcase
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
		$manager = MShop_Supplier_Manager_Factory::createManager( TestHelper::getContext() );

		$listManager = $manager->getSubManager( 'list' );
		$this->_object = $listManager->getSubManager( 'type' );
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
		$search->setConditions( $search->compare( '==', 'supplier.list.type.editor', $this->_editor) );
		$results = $this->_object->searchItems($search);

		if( ( $expected = reset($results) ) === false ) {
			throw new Exception( 'No attribute list type item found' );
		}

		$this->assertEquals( $expected, $this->_object->getItem( $expected->getId() ) );
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'supplier.list.type.editor', $this->_editor) );
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

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}

	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'supplier.list.type.id', null );
		$expr[] = $search->compare( '!=', 'supplier.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'supplier.list.type.domain', 'supplier' );
		$expr[] = $search->compare( '==', 'supplier.list.type.code', 'default' );
		$expr[] = $search->compare( '==', 'supplier.list.type.label', 'Default' );
		$expr[] = $search->compare( '==', 'supplier.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'supplier.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'supplier.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'supplier.list.type.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}

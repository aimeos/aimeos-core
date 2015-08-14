<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Catalog_Manager_Default.
 */
class MShop_Catalog_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Catalog_Manager_Default( TestHelper::getContext() );
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

		$this->assertInstanceOf( 'MShop_Catalog_Item_Interface', $item );
		$this->assertEquals( TestHelper::getContext()->getLocale()->getSiteId(), $item->getNode()->siteid );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch() );
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch( true ) );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'catalog.id', null );
		$expr[] = $search->compare( '!=', 'catalog.siteid', null );
		$expr[] = $search->compare( '==', 'catalog.code', 'cafe' );
		$expr[] = $search->compare( '==', 'catalog.level', 2 );
		$expr[] = $search->compare( '==', 'catalog.left', 3 );
		$expr[] = $search->compare( '==', 'catalog.right', 4 );
		$expr[] = $search->compare( '==', 'catalog.status', 1 ) ;
		$expr[] = $search->compare( '==', 'catalog.label', 'Kaffee' );
		$expr[] = $search->compare( '~=', 'catalog.config', '{' );
		$expr[] = $search->compare( '>=', 'catalog.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'catalog.list.id', null );
		$expr[] = $search->compare( '!=', 'catalog.list.siteid', null );
		$expr[] = $search->compare( '!=', 'catalog.list.parentid', null );
		$expr[] = $search->compare( '!=', 'catalog.list.typeid', null );
		$expr[] = $search->compare( '!=', 'catalog.list.refid', null );
		$expr[] = $search->compare( '>=', 'catalog.list.datestart', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.dateend', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.status', 1 );
		$expr[] = $search->compare( '!=', 'catalog.list.config', null );
		$expr[] = $search->compare( '>=', 'catalog.list.position', 0 ) ;
		$expr[] = $search->compare( '>=', 'catalog.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'catalog.list.type.id', null );
		$expr[] = $search->compare( '!=', 'catalog.list.type.siteid', null );
		$expr[] = $search->compare( '>=', 'catalog.list.type.code', '' );
		$expr[] = $search->compare( '==', 'catalog.list.type.status', 1 ) ;
		$expr[] = $search->compare( '>=', 'catalog.list.type.label', '' );
		$expr[] = $search->compare( '>=', 'catalog.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'catalog.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'catalog.list.type.editor', $this->_editor );


		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice(0, 1);

		$items = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, $total );
		$this->assertEquals( 1, count( $items ) );

		foreach( $items as $itemId => $item ){
			$this->assertInstanceOf( 'MShop_Catalog_Item_Interface', $item );
			$this->assertEquals( $itemId, $item->getId() );
		}

		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Misc' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Catalog item not found' );
		}

		$this->assertEquals( 'Sonstiges', $item->getName() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Root' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search, array('text') );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Catalog item not found' );
		}

		$testItem = $this->_object->getItem( $item->getId() );

		$this->assertEquals( TestHelper::getContext()->getLocale()->getSiteId(), $testItem->getSiteId() );
		$this->assertEquals( $item->getId(), $testItem->getId() );
		$this->assertEquals( 'Root', $testItem->getLabel() );
		$this->assertEquals( 'Root', $testItem->getName() );
		$this->assertEquals( 1, $testItem->getStatus() );
	}


	public function testGetTree()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.code', 'root' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Catalog item not found' );
		}

		$rootItem = $this->_object->getTree( $item->getId(), array( 'text' ), MW_Tree_Manager_Abstract::LEVEL_TREE );
		$categoryItem = $rootItem->getChild( 0 );
		$miscItem = $categoryItem->getChild( 2 );

		$this->assertEquals( TestHelper::getContext()->getLocale()->getSiteId(), $miscItem->getSiteId() );
		$this->assertEquals( 'Misc', $miscItem->getLabel() );
		$this->assertEquals( 'Sonstiges', $miscItem->getName() );
	}


	public function testGetTreeWithConditions()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.code', array( 'root', 'categories' ) ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search );
		$parentIds = array();

		foreach( $items as $item ) {
			$parentIds[] = $item->getId();
		}

		if( count( $parentIds ) != 2 ) {
			throw new Exception( 'Not all categories found!' );
		}

		$parentIds[] = 0;

		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.parentid', $parentIds ) );

		$tree = $this->_object->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_TREE, $search );

		$categorycat = $tree->getChild(0);
		$groupcat = $tree->getChild(1);
		$groupcatChildren = $groupcat->getChildren();
		$categorycatChildren = $categorycat->getChildren();
		$cafecat = $categorycat->getChild(0);

		$caffein = $this->_object->createItem();
		$caffein->setCode('caffein');
		$caffein->setLabel('Caffein');

		$this->_object->insertItem( $caffein, $cafecat->getId() );
		$this->_object->deleteItem($caffein->getId());

		$this->assertEquals( 0, $tree->getNode()->parentid );
		$this->assertEquals( 'categories', $categorycat->getCode() );
		$this->assertEquals( 'group', $groupcat->getCode() );
		$this->assertEquals( $tree->getId(), $categorycat->getNode()->parentid );
		$this->assertEquals( $tree->getId(), $groupcat->getNode()->parentid );
		$this->assertEquals( $categorycat->getId(), $cafecat->getNode()->parentid );
		$this->assertEquals( $cafecat->getId(), $caffein->getNode()->parentid );
		$this->assertEquals( array(), $groupcatChildren );
		$this->assertEquals( 3, count( $categorycatChildren ) );
	}


	public function testGetPath()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Kaffee' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems($search, array( 'text' ) );

		if ( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'Catalog item not found' );
		}

		$items = $this->_object->getPath( $item->getId() );
		$expected = array('Root', 'Categories', 'Kaffee');

		foreach ( $items as $item ) {
			$this->assertEquals(array_shift($expected), $item->getLabel());
		}

		$this->assertEquals( 0, count( $expected ) );
	}


	public function testSaveInsertMoveDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'catalog.label', 'Root' ),
			$search->compare( '==', 'catalog.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->_object->searchItems( $search, array('text')  );

		if ( ( $item = reset($items) ) === false ) {
			throw new Exception( 'No root node found' );
		}

		$parentId = $item->getId();
		$item->setId(null);
		$item->setLabel( 'Root child' );
		$item->setCode( 'new Root child' );
		$this->_object->insertItem( $item, $parentId );
		$this->_object->moveItem( $item->getId(), $parentId, $parentId );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setStatus( true );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $item->getId() );
	}


	public function testGetSubManager()
	{
		$target = 'MShop_Common_Manager_Interface';
		$this->assertInstanceOf( $target, $this->_object->getSubManager('list') );
		$this->assertInstanceOf( $target, $this->_object->getSubManager('list', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('list', 'unknown');
	}
}
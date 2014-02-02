<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Attribute_Manager_Default.
 */
class MShop_Attribute_Manager_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Attribute_Manager_Default
	 * @access protected
	 */
	private $_object;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Attribute_Manager_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );
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
		MShop_Factory::clear();
	}


	public function testGetSearchAttributes()
	{
		foreach($this->_object->getSearchAttributes() as $obj) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Attribute_Item_Interface', $this->_object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list', 'default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('list', 'unknown');
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.code', 'm' ),
			$search->compare( '==', 'attribute.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$results = $this->_object->searchItems( $search, array( 'text' ) );
		if( ( $itemA = reset($results) ) === false) {
			throw new Exception( 'No search results available in testGetItem()' );
		}

		$itemB = $this->_object->getItem( $itemA->getId(), array( 'text' ) );

		$this->assertEquals( $itemA->getId(), $itemB->getId() );
		$this->assertEquals( 1, count( $itemB->getListItems( 'text' ) ) );
		$this->assertEquals( 1, count( $itemB->getRefItems( 'text' ) ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$typeManager = $this->_object->getSubManager( 'type' );
		$search = $typeManager->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.type.code', 'size' ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.type.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$typeItems = $typeManager->searchItems( $search );

		if( ( $this->typeItem = reset($typeItems) ) === false ) {
			throw new Exception('No attribute type item available in setUp()');
		}

		$item = $this->_object->createItem();
		$item->setId( null );
		$item->setDomain( 'tmpDomainx' );
		$item->setCode( '106x' );
		$item->setLabel( '106x' );
		$item->setTypeId( $this->typeItem->getId() );
		$item->setPosition( 0 );
		$item->setStatus( 7 );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setDomain( 'tmpDomain');
		$itemExp->setCode( '106' );
		$itemExp->setLabel( '106' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $item->getId() );
	}


	public function testCreateSearch()
	{
		$search = $this->_object->createSearch();
		$this->assertInstanceOf('MW_Common_Criteria_Interface', $search);
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'attribute.id', null );
		$expr[] = $search->compare( '!=', 'attribute.siteid', null);
		$expr[] = $search->compare( '!=', 'attribute.typeid', null );
		$expr[] = $search->compare( '==', 'attribute.position', 5 );
		$expr[] = $search->compare( '==', 'attribute.code', 'black' );
		$expr[] = $search->compare( '==', 'attribute.label', 'black' );
		$expr[] = $search->compare( '==', 'attribute.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.status', 0 );
		$expr[] = $search->compare( '>=', 'attribute.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'attribute.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.type.code', 'color' );
		$expr[] = $search->compare( '==', 'attribute.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'attribute.type.label', 'Color' );
		$expr[] = $search->compare( '==', 'attribute.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.type.editor', $this->_editor );

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
		$expr[] = $search->compare( '==', 'attribute.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'attribute.list.type.id', null );
		$expr[] = $search->compare( '!=', 'attribute.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'attribute.list.type.code', 'default' );
		$expr[] = $search->compare( '==', 'attribute.list.type.domain', 'attribute' );
		$expr[] = $search->compare( '==', 'attribute.list.type.label', 'Default' );
		$expr[] = $search->compare( '==', 'attribute.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'attribute.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'attribute.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'attribute.list.type.editor', $this->_editor );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals(1, count( $results ) );
		$this->assertEquals(1, $total);

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '~=', 'attribute.code', '3' ),
			$search->compare( '==', 'attribute.editor', $this->_editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice(0, 5);

		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 10, $total );
		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testSearchTotal()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'attribute.type.code', 'size' ),
			$search->compare( '==', 'attribute.type.domain', 'product' ),
			$search->compare( '==', 'attribute.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);

		$total = 0;
		$items = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 6, $total );
	}
}

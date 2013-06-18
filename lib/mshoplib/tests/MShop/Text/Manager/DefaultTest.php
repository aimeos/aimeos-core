<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14843 2012-01-13 08:11:39Z nsendetzky $
 */

/**
 * Test class for MShop_Text_Manager_Default.
 */
class MShop_Text_Manager_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var MShop_Text_Manager_Default
	 */
	private $_object;

	/**
	 * @var string
	 * @access protected
	 */
	private $_editor = '';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_editor = TestHelper::getContext()->getEditor();
		$this->_object = new MShop_Text_Manager_Default( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Text_Manager_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testCreateItem()
	{
		$textItem = $this->_object->createItem();
		$this->assertInstanceOf( 'MShop_Text_Item_Interface', $textItem );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'text.id', null );
		$expr[] = $search->compare( '!=', 'text.siteid', null);
		$expr[] = $search->compare( '==', 'text.languageid', 'de' );
		$expr[] = $search->compare( '>', 'text.typeid', 0 );
		$expr[] = $search->compare( '>=', 'text.label', '' );
		$expr[] = $search->compare( '==', 'text.domain', 'catalog' );
		$expr[] = $search->compare( '~=', 'text.content', 'Lange Beschreibung' );
		$expr[] = $search->compare( '==', 'text.status', 1 );
		$expr[] = $search->compare( '>=', 'text.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'text.type.id', null );
		$expr[] = $search->compare( '!=', 'text.type.siteid', null );
		$expr[] = $search->compare( '==', 'text.type.code', 'long' );
		$expr[] = $search->compare( '==', 'text.type.domain', 'catalog' );
		$expr[] = $search->compare( '==', 'text.type.label', 'Long description' );
		$expr[] = $search->compare( '==', 'text.type.status', 1 );
		$expr[] = $search->compare( '>=', 'text.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.type.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'text.list.id', null );
		$expr[] = $search->compare( '!=', 'text.list.siteid', null );
		$expr[] = $search->compare( '>', 'text.list.parentid', 0 );
		$expr[] = $search->compare( '==', 'text.list.domain', 'media' );
		$expr[] = $search->compare( '>', 'text.list.typeid', 0 );
		$expr[] = $search->compare( '>', 'text.list.refid', 0 );
		$expr[] = $search->compare( '==', 'text.list.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.list.dateend', '2022-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.list.position', 0 );
		$expr[] = $search->compare( '>=', 'text.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'text.list.type.id', null );
		$expr[] = $search->compare( '!=', 'text.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'text.list.type.code', 'align-top' );
		$expr[] = $search->compare( '==', 'text.list.type.domain', 'media' );
		$expr[] = $search->compare( '>', 'text.list.type.label', '' );
		$expr[] = $search->compare( '==', 'text.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'text.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.list.type.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		//search without base criteria
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'text.editor', $this->_editor ) );
		$this->assertEquals( 86, count( $this->_object->searchItems( $search ) ) );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'text.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 5 );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 82, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'text.content', '%Monetary%'),
			$search->compare( '==', 'text.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->_object->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No text item including "%1$s" found', '%Monetary%' ) );
		}


		$actual = $this->_object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'text.content', 'Cafe Noire Expresso'),
			$search->compare( '==', 'text.editor', $this->_editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$a = $this->_object->searchItems( $search );

		if ( ( $item = reset( $a ) ) === false ) {
			throw new Exception('Text item not found.');
		}

		$item->setId( null );
		$item->setLabel('Cafe Noire Unittest');
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel('Cafe Noire Expresso x');
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getContent(), $itemSaved->getContent() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getContent(), $itemUpd->getContent() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem($itemSaved->getId());
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('list', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('list', 'unknown');
	}
}
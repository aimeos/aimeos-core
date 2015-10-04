<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Text_Manager_Standard.
 */
class MShop_Text_Manager_StandardTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var MShop_Text_Manager_Standard
	 */
	private $object;

	/**
	 * @var string
	 * @access protected
	 */
	private $editor = '';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = new MShop_Text_Manager_Standard( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$textItem = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Text_Item_Iface', $textItem );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Iface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'text.id', null );
		$expr[] = $search->compare( '!=', 'text.siteid', null );
		$expr[] = $search->compare( '==', 'text.languageid', 'de' );
		$expr[] = $search->compare( '>', 'text.typeid', 0 );
		$expr[] = $search->compare( '>=', 'text.label', '' );
		$expr[] = $search->compare( '==', 'text.domain', 'catalog' );
		$expr[] = $search->compare( '~=', 'text.content', 'Lange Beschreibung' );
		$expr[] = $search->compare( '==', 'text.status', 1 );
		$expr[] = $search->compare( '>=', 'text.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'text.type.id', null );
		$expr[] = $search->compare( '!=', 'text.type.siteid', null );
		$expr[] = $search->compare( '==', 'text.type.code', 'long' );
		$expr[] = $search->compare( '==', 'text.type.domain', 'catalog' );
		$expr[] = $search->compare( '==', 'text.type.label', 'Long description' );
		$expr[] = $search->compare( '==', 'text.type.status', 1 );
		$expr[] = $search->compare( '>=', 'text.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.type.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'text.lists.id', null );
		$expr[] = $search->compare( '!=', 'text.lists.siteid', null );
		$expr[] = $search->compare( '>', 'text.lists.parentid', 0 );
		$expr[] = $search->compare( '==', 'text.lists.domain', 'media' );
		$expr[] = $search->compare( '>', 'text.lists.typeid', 0 );
		$expr[] = $search->compare( '>', 'text.lists.refid', 0 );
		$expr[] = $search->compare( '==', 'text.lists.datestart', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.lists.dateend', '2022-01-01 00:00:00' );
		$expr[] = $search->compare( '!=', 'text.lists.config', null );
		$expr[] = $search->compare( '==', 'text.lists.position', 0 );
		$expr[] = $search->compare( '==', 'text.lists.status', 1 );
		$expr[] = $search->compare( '>=', 'text.lists.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.lists.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.lists.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'text.lists.type.id', null );
		$expr[] = $search->compare( '!=', 'text.lists.type.siteid', null );
		$expr[] = $search->compare( '==', 'text.lists.type.code', 'align-top' );
		$expr[] = $search->compare( '==', 'text.lists.type.domain', 'media' );
		$expr[] = $search->compare( '>', 'text.lists.type.label', '' );
		$expr[] = $search->compare( '==', 'text.lists.type.status', 1 );
		$expr[] = $search->compare( '>=', 'text.lists.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'text.lists.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'text.lists.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );

		//search without base criteria
		$search = $this->object->createSearch();
		$search->setConditions( $search->compare( '==', 'text.editor', $this->editor ) );
		$this->assertEquals( 87, count( $this->object->searchItems( $search ) ) );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'text.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 5 );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 5, count( $results ) );
		$this->assertEquals( 83, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'text.content', '%Monetary%' ),
			$search->compare( '==', 'text.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No text item including "%1$s" found', '%Monetary%' ) );
		}


		$actual = $this->object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'text.content', 'Cafe Noire Expresso' ),
			$search->compare( '==', 'text.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$a = $this->object->searchItems( $search );

		if( ( $item = reset( $a ) ) === false ) {
			throw new Exception( 'Text item not found.' );
		}

		$item->setId( null );
		$item->setLabel( 'Cafe Noire Unittest' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setLabel( 'Cafe Noire Expresso x' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


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

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
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

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}
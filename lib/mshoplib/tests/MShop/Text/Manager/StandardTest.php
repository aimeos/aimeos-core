<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2017
 */

namespace Aimeos\MShop\Text\Manager;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $editor = '';


	protected function setUp()
	{
		$this->editor = \TestHelperMShop::getContext()->getEditor();
		$this->object = new \Aimeos\MShop\Text\Manager\Standard( \TestHelperMShop::getContext() );
	}


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
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Text\\Item\\Iface', $textItem );
	}


	public function testGetResourceType()
	{
		$result = $this->object->getResourceType();

		$this->assertContains( 'text', $result );
		$this->assertContains( 'text/type', $result );
		$this->assertContains( 'text/lists', $result );
		$this->assertContains( 'text/lists/type', $result );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( '\\Aimeos\\MW\\Criteria\\Attribute\\Iface', $attribute );
		}
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = [];
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
		$result = $this->object->searchItems( $search, [], $total );
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
		$results = $this->object->searchItems( $search, [], $total );
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
			$search->compare( '~=', 'text.content', 'Monetary' ),
			$search->compare( '==', 'text.editor', $this->editor ),
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$result = $this->object->searchItems( $search );

		if( ( $expected = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No text item including "%1$s" found', 'Monetary' ) );
		}


		$actual = $this->object->getItem( $expected->getId() );
		$this->assertNotEquals( '', $actual->getTypeName() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveInvalid()
	{
		$this->setExpectedException( '\Aimeos\MShop\Text\Exception' );
		$this->object->saveItem( new \Aimeos\MShop\Locale\Item\Standard() );
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
			throw new \RuntimeException( 'Text item not found.' );
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

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'type', 'Standard' ) );

		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists' ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Common\\Manager\\Iface', $this->object->getSubManager( 'lists', 'Standard' ) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->getSubManager( 'lists', 'unknown' );
	}
}

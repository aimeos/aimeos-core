<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Media_Manager_Default
 */
class MShop_Media_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object = null;
	private $editor = '';


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = MShop_Media_Manager_Factory::createManager( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}

	public function testGetSearchAttributes()
	{
		foreach( $this->object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}

	public function testCreateItem()
	{
		$item = $this->object->createItem();
		$this->assertInstanceOf( 'MShop_Media_Item_Interface', $item );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->object->createSearch() );
	}

	public function testSearchItem()
	{
		//search without base criteria
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'media.id', null );
		$expr[] = $search->compare( '!=', 'media.siteid', null );
		$expr[] = $search->compare( '==', 'media.languageid', 'de' );
		$expr[] = $search->compare( '>', 'media.typeid', 0 );
		$expr[] = $search->compare( '==', 'media.domain', 'product' );
		$expr[] = $search->compare( '==', 'media.label', 'cn_colombie_266x221' );
		$expr[] = $search->compare( '==', 'media.url', 'prod_266x221/198_prod_266x221.jpg' );
		$expr[] = $search->compare( '==', 'media.preview', '' );
		$expr[] = $search->compare( '==', 'media.mimetype', '' );
		$expr[] = $search->compare( '==', 'media.status', 1 );
		$expr[] = $search->compare( '>=', 'media.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'media.type.id', null );
		$expr[] = $search->compare( '!=', 'media.type.siteid', null );
		$expr[] = $search->compare( '==', 'media.type.domain', 'product' );
		$expr[] = $search->compare( '==', 'media.type.code', 'prod_266x221' );
		$expr[] = $search->compare( '>', 'media.type.label', '' );
		$expr[] = $search->compare( '==', 'media.type.status', 1 );
		$expr[] = $search->compare( '>=', 'media.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'media.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'media.type.editor', $this->editor );

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
		$expr[] = $search->compare( '==', 'media.list.editor', $this->editor );

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

		//search with base criteria
		$search = $this->object->createSearch( true );
		$conditions = array(
			$search->compare( '==', 'media.editor', $this->editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 4 );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 4, count( $results ) );
		$this->assertEquals( 11, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}

	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '==', 'media.label', 'cn_colombie_123x103' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No media item with label "cn_colombie_123x103" found' );
		}

		$this->assertEquals( $item, $this->object->getItem( $item->getId() ) );
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'media.label', 'example' ),
			$search->compare( '==', 'media.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$items = $this->object->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No media item with label "cn_colombie_123x103" found' );
		}

		$item->setId( null );
		$item->setLanguageId( 'de' );
		$item->setDomain( 'test_dom' );
		$item->setLabel( 'test' );
		$item->setMimeType( 'image/jpeg' );
		$item->setUrl( 'test.jpg' );
		$item->setPreview( 'xxxtest-preview.jpg' );

		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setPreview( 'test-preview.jpg' );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLanguageId(), $itemSaved->getLanguageId() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getMimeType(), $itemSaved->getMimeType() );
		$this->assertEquals( $item->getUrl(), $itemSaved->getUrl() );
		$this->assertEquals( $item->getPreview(), $itemSaved->getPreview() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLanguageId(), $itemUpd->getLanguageId() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getMimeType(), $itemUpd->getMimeType() );
		$this->assertEquals( $itemExp->getUrl(), $itemUpd->getUrl() );
		$this->assertEquals( $itemExp->getPreview(), $itemUpd->getPreview() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $item->getId() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type', 'Default' ) );

		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'list' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'list', 'Default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'list', 'unknown' );
	}
}

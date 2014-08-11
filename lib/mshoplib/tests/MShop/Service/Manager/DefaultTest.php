<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Manager_Default.
 */
class MShop_Service_Manager_DefaultTest extends MW_Unittest_Testcase
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
		$this->_object = new MShop_Service_Manager_Default( TestHelper::getContext() );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}

	public function testCreateItem()
	{
		$this->assertInstanceOf('MShop_Service_Item_Interface', $this->_object->createItem());
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitcode' ),
			$search->compare( '==', 'service.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$results = $this->_object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No service provider item found.' );
		}

		$item->setId(null);
		$item->setCode( 'newstaticdelivery' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( '2ndChang' );
		$itemExp->setLabel( '2ndNameChanged' );
		$itemExp->setPosition( '1' );
		$itemExp->setStatus( '1' );
		$itemExp->setProvider( 'HS' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem($itemSaved->getId());
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.code', 'unitcode'),
			$search->compare( '==', 'service.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search, array( 'text' ) );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception('No item found');
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId(), array( 'text' ) ) );
		$this->assertEquals( 2, count( $item->getRefItems('text') ) );
	}


	public function testSearchItem()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'service.id', null );
		$expr[] = $search->compare( '!=', 'service.siteid', null);
		$expr[] = $search->compare( '>', 'service.typeid', 0 );
		$expr[] = $search->compare( '>=', 'service.position', 0 );
		$expr[] = $search->compare( '==', 'service.code', 'unitcode' );
		$expr[] = $search->compare( '==', 'service.label', 'unitlabel' );
		$expr[] = $search->compare( '==', 'service.provider', 'Default' );
		$expr[] = $search->compare( '~=', 'service.config', 'url' );
		$expr[] = $search->compare( '==', 'service.status', 1 );
		$expr[] = $search->compare( '>=', 'service.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'service.type.id', null );
		$expr[] = $search->compare( '!=', 'service.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.type.code', 'delivery' );
		$expr[] = $search->compare( '==', 'service.type.domain', 'service' );
		$expr[] = $search->compare( '==', 'service.type.label', 'Delivery' );
		$expr[] = $search->compare( '==', 'service.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.type.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'service.list.id', null );
		$expr[] = $search->compare( '!=', 'service.list.siteid', null );
		$expr[] = $search->compare( '>', 'service.list.parentid', 0 );
		$expr[] = $search->compare( '==', 'service.list.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.list.typeid', 0 );
		$expr[] = $search->compare( '>', 'service.list.refid', 0 );
		$expr[] = $search->compare( '==', 'service.list.datestart', null );
		$expr[] = $search->compare( '==', 'service.list.dateend', null );
		$expr[] = $search->compare( '!=', 'service.list.config', null );
		$expr[] = $search->compare( '==', 'service.list.position', 0 );
		$expr[] = $search->compare( '==', 'service.list.status', 1 );
		$expr[] = $search->compare( '>=', 'service.list.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.list.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.list.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'service.list.type.id', null );
		$expr[] = $search->compare( '!=', 'service.list.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.list.type.code', 'unittype1' );
		$expr[] = $search->compare( '==', 'service.list.type.domain', 'text' );
		$expr[] = $search->compare( '>', 'service.list.type.label', '' );
		$expr[] = $search->compare( '==', 'service.list.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.list.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.list.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.list.type.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->compare( '==', 'service.provider', 'unitprovider' ),
			$search->compare( '==', 'service.editor', $this->_editor ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 0, count( $this->_object->searchItems( $search ) ) );
	}


	public function testGetProvider()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.type.code', 'delivery' ),
			$search->compare( '==', 'service.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice( 0, 1 );
		$result = $this->_object->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No service item found' );
		}

		$item->setProvider( 'Default,Example');
		$provider = $this->_object->getProvider( $item );

		$this->assertInstanceOf('MShop_Service_Provider_Interface', $provider );
		$this->assertInstanceOf('MShop_Service_Provider_Decorator_Example', $provider );


		$this->setExpectedException('MShop_Exception');
		$this->_object->getProvider( $this->_object->createItem() );
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


	public function testGetSearchAttributes()
	{
		$attribs = $this->_object->getSearchAttributes();
		foreach($attribs as $obj) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $obj );
		}

	}


	public function testCreateSearch()
	{
		$search = $this->_object->createSearch();
		$this->assertInstanceOf('MW_Common_Criteria_Interface', $search);
	}
}
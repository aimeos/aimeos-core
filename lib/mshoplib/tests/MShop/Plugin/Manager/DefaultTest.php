<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class MShop_Plugin_Manager_DefaultTest_Publisher extends MW_Observer_Publisher_Abstract
{
}


/**
 * Test class for MShop_Plugin_Manager_Default.
 */
class MShop_Plugin_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $examplePlugin;
	private $examplePlugin2;
	private $editor = '';


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->editor = TestHelper::getContext()->getEditor();
		$this->object = MShop_Plugin_Manager_Factory::createManager( TestHelper::getContext() );

		$type = $this->object->getSubManager( 'type' );
		$search = $type->createSearch();
		$conditions = array(
			$search->compare( '==', 'plugin.type.code', 'order' ),
			$search->compare( '==', 'plugin.type.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $type->searchItems( $search );
		if( ( $typeItem = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->examplePlugin = $this->object->createItem();
		$this->examplePlugin->setTypeId( $typeItem->getId() );

		$this->examplePlugin->setProvider( 'Example' );
		$this->examplePlugin->setConfig( array( "limit" => "10" ) );
		$this->examplePlugin->setStatus( 1 );

		$this->examplePlugin2 = clone $this->examplePlugin;
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->context );
	}


	public function testCleanup()
	{
		$this->object->cleanup( array( -1 ) );
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Plugin_Item_Interface', $this->object->createItem() );
	}


	public function testRegister()
	{
		$publisher = new MShop_Plugin_Manager_DefaultTest_Publisher();
		$this->object->register( $publisher, 'order' );
	}


	public function testGetItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->object->searchItems( $search );
		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No plugin item including "%1$s" found', 'Shipping' ) );
		}

		$actual = $this->object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$a = $this->object->searchItems( $search );
		if( ( $item = reset( $a ) ) === false ) {
			throw new Exception( 'Search provider in test failt' );
		}

		$item->setId( null );
		$item->setProvider( 'Example1' );
		$this->object->saveItem( $item );
		$itemSaved = $this->object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProvider( 'Example' );
		$itemExp->setPosition( 5 );
		$itemExp->setStatus( -1 );
		$this->object->saveItem( $itemExp );
		$itemUpd = $this->object->getItem( $itemExp->getId() );

		$this->object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getPosition(), $itemSaved->getPosition() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getPosition(), $itemUpd->getPosition() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getItem( $itemSaved->getId() );
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'plugin.id', null );
		$expr[] = $search->compare( '!=', 'plugin.siteid', null );
		$expr[] = $search->compare( '!=', 'plugin.typeid', null );
		$expr[] = $search->compare( '!=', 'plugin.label', null );
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit' );
		$expr[] = $search->compare( '~=', 'plugin.config', 'single-number-max' );
		$expr[] = $search->compare( '==', 'plugin.position', 0 );
		$expr[] = $search->compare( '==', 'plugin.status', 1 );
		$expr[] = $search->compare( '>=', 'plugin.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'plugin.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );

		$expr[] = $search->compare( '!=', 'plugin.type.id', null );
		$expr[] = $search->compare( '!=', 'plugin.type.siteid', null );
		$expr[] = $search->compare( '==', 'plugin.type.code', 'order' );
		$expr[] = $search->compare( '==', 'plugin.type.domain', 'plugin' );
		$expr[] = $search->compare( '==', 'plugin.type.label', 'Order' );
		$expr[] = $search->compare( '==', 'plugin.type.status', 1 );
		$expr[] = $search->compare( '>=', 'plugin.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'plugin.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'plugin.type.editor', $this->editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );

		$expr = $conditions = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'Shipping,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'BasketLimits,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->editor );
		$conditions[] = $search->combine( '&&', $expr );

		//search without base criteria
		$search = $this->object->createSearch();
		$search->setConditions( $search->combine( '||', $conditions ) );
		$this->assertEquals( 3, count( $this->object->searchItems( $search ) ) );

		//search with base criteria
		$search = $this->object->createSearch( true );
		$expr = array(
			$search->combine( '||', $conditions ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$total = 0;
		$search->setSlice( 0, 2 );
		$this->assertEquals( 2, count( $this->object->searchItems( $search, array(), $total ) ) );
		$this->assertEquals( 3, $total );

		foreach( $results as $itemId => $item ) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->object->getSubManager( 'type', 'Default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->object->getSubManager( 'type', 'unknown' );
	}
}

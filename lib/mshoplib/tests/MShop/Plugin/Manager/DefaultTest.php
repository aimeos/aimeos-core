<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14854 2012-01-13 12:54:14Z doleiynyk $
 */


class MShop_Plugin_Manager_DefaultTest_Publisher extends MW_Observer_Publisher_Abstract
{
}


/**
 * Test class for MShop_Plugin_Manager_Default.
 */
class MShop_Plugin_Manager_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_examplePlugin;
	protected $_examplePlugin2;

	/**
	 * @var string
	 * @access protected
	 */
	protected $_editor = '';

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Plugin_Manager_DefaultTest');
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
		$this->_object = MShop_Plugin_Manager_Factory::createManager(TestHelper::getContext());

		$type = $this->_object->getSubManager('type');
		$search = $type->createSearch();
		$conditions = array(
			$search->compare( '==', 'plugin.type.code', 'order' ),
			$search->compare( '==', 'plugin.type.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $type->searchItems($search);
		if ( ( $typeItem = reset($results) ) === false ) {
			throw new Exception('No item found');
		}

		$this->_examplePlugin = $this->_object->createItem();
		$this->_examplePlugin->setTypeId($typeItem->getId());

		$this->_examplePlugin->setProvider('Example');
		$this->_examplePlugin->setConfig( array( "limit" => "10" ) );
		$this->_examplePlugin->setStatus( 1 );

		$this->_examplePlugin2 = clone $this->_examplePlugin;
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Plugin_Item_Interface', $this->_object->createItem() );
	}


	public function testRegister()
	{
		$publisher = new MShop_Plugin_Manager_DefaultTest_Publisher();
		$this->_object->register( $publisher, 'order' );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping' ),
			$search->compare( '==', 'plugin.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$result = $this->_object->searchItems( $search );
		if( ( $expected = reset( $result ) ) === false ) {
			throw new Exception( sprintf( 'No plugin item including "%1$s" found', 'Shipping' ) );
		}

		$actual = $this->_object->getItem( $expected->getId() );

		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '~=', 'plugin.provider', 'Shipping'),
			$search->compare( '==', 'plugin.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );

		$a = $this->_object->searchItems( $search );
		if ( ($item = reset( $a )) === false) {
			throw new Exception('Search provider in test failt');
		}

		$item->setId( null );
		$item->setProvider( 'Example1' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setProvider( 'Example' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $itemSaved->getType() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getTypeId(), $itemSaved->getTypeId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertTrue( $itemUpd->getType() !== null );
		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getTypeId(), $itemUpd->getTypeId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem($itemSaved->getId());
	}


	public function testSearchItems()
	{
		$total = 0;
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'plugin.id', null );
		$expr[] = $search->compare( '!=', 'plugin.siteid', null );
		$expr[] = $search->compare( '!=', 'plugin.typeid', null );
		$expr[] = $search->compare( '!=', 'plugin.label', null );
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit' );
		$expr[] = $search->compare( '~=', 'plugin.config', 'single-number-max' );
		$expr[] = $search->compare( '==', 'plugin.status', 1 );
		$expr[] = $search->compare( '>=', 'plugin.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'plugin.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->_editor );

		$expr[] = $search->compare( '!=', 'plugin.type.id', null );
		$expr[] = $search->compare( '!=', 'plugin.type.siteid', null );
		$expr[] = $search->compare( '==', 'plugin.type.code', 'order' );
		$expr[] = $search->compare( '==', 'plugin.type.domain', 'plugin' );
		$expr[] = $search->compare( '==', 'plugin.type.label', 'Order' );
		$expr[] = $search->compare( '==', 'plugin.type.status', 1 );
		$expr[] = $search->compare( '>=', 'plugin.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'plugin.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'plugin.type.editor', $this->_editor );

		$search->setConditions( $search->combine('&&', $expr) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );

		$expr = $conditions = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'Shipping,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->_editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'ProductLimit,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->_editor );
		$conditions[] = $search->combine( '&&', $expr );
		$expr = array();
		$expr[] = $search->compare( '~=', 'plugin.provider', 'BasketLimits,Example' );
		$expr[] = $search->compare( '==', 'plugin.editor', $this->_editor );
		$conditions[] = $search->combine( '&&', $expr );

		//search without base criteria
		$search = $this->_object->createSearch();
		$search->setConditions( $search->combine( '||', $conditions ) );
		$this->assertEquals( 3, count( $this->_object->searchItems( $search ) ) );

		//search with base criteria
		$search = $this->_object->createSearch( true );
		$expr = array(
			$search->combine( '||', $conditions ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$total = 0;
		$search->setSlice( 0, 2 );
		$this->assertEquals( 2, count( $this->_object->searchItems( $search, array(), $total ) ) );
		$this->assertEquals( 3, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type') );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager('type', 'Default') );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('type', 'unknown');
	}
}

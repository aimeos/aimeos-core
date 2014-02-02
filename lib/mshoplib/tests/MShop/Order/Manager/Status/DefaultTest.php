<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Order_Manager_Status_Default.
 */

class MShop_Order_Manager_Status_DefaultTest extends MW_Unittest_Testcase
{
	private $_context;
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Manager_Default_DefaultTest');
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
		$this->_context = TestHelper::getContext();
		$this->_object = new MShop_Order_Manager_Status_Default($this->_context);
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
		MShop_Factory::clear();
	}

	public function testCreateItem()
	{
		$this->assertInstanceOf('MShop_Order_Item_Status_Interface', $this->_object->createItem());
	}

	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare('==', 'order.status.value', 'shipped') );
		$results = $this->_object->searchItems($search);

		if ( ($expected = reset($results)) === false ) {
			throw new MShop_Order_Exception('No order status item found');
		}

		$this->assertEquals($expected, $this->_object->getItem($expected->getId()));
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'order.status.value', 'shipped' ),
			$search->compare( '==', 'order.status.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No order base item found.' );
		}

		$item->setId(null);
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setType( 'received' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getParentId(), $itemSaved->getParentId() );
		$this->assertEquals( $item->getType(), $itemSaved->getType() );
		$this->assertEquals( $item->getValue(), $itemSaved->getValue() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getParentId(), $itemUpd->getParentId() );
		$this->assertEquals( $itemExp->getType(), $itemUpd->getType() );
		$this->assertEquals( $itemExp->getValue(), $itemUpd->getValue() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $itemSaved->getId() );
	}

	public function testCreateSearch()
	{
		$this->assertInstanceOf('MW_Common_Criteria_Interface', $this->_object->createSearch() );
	}

	public function testSearchItems()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'order.status.id', null );
		$expr[] = $search->compare( '==', 'order.status.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.status.parentid', null );
		$expr[] = $search->compare( '>=', 'order.status.type', 'typestatus' );
		$expr[] = $search->compare( '==', 'order.status.value', 'shipped' );
		$expr[] = $search->compare( '>=', 'order.status.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.status.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'order.status.editor', $this->_editor );



		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( 1, $total );


		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '>=', 'order.status.value', 'waiting' ),
			$search->compare( '==', 'order.status.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$total = 0;
		$items = $this->_object->searchItems($search, array(), $total);
		$this->assertEquals( 1, count( $items ) );
		$this->assertEquals( 2, $total );

		foreach($items as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager( 'unknown' );
	}



}
<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Service_Manager_Type_Default.
 */
class MShop_Service_Manager_Type_DefaultTest extends MW_Unittest_Testcase
{
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
		require_once 'PHPUnit/ServiceUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Service_Manager_Type_DefaultTest');
		$result = PHPUnit_ServiceUI_TestRunner::run($suite);
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
		$manager = MShop_Service_Manager_Factory::createManager( TestHelper::getContext() );
		$this->_object = $manager->getSubManager( 'type' );
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
		$this->assertInstanceOf( 'MShop_Common_Item_Type_Interface', $this->_object->createItem() );
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

		$expr[] = $search->compare( '!=', 'service.type.id', null );
		$expr[] = $search->compare( '!=', 'service.type.siteid', null );
		$expr[] = $search->compare( '==', 'service.type.code', 'delivery' );
		$expr[] = $search->compare( '==', 'service.type.domain', 'service' );
		$expr[] = $search->compare( '==', 'service.type.label', 'Delivery' );
		$expr[] = $search->compare( '==', 'service.type.status', 1 );
		$expr[] = $search->compare( '>=', 'service.type.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'service.type.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'service.type.editor', $this->_editor );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );


		//search with base criteria
		$search = $this->_object->createSearch(true);
		$conditions = array(
			$search->compare( '==', 'service.type.editor', $this->_editor ),
			$search->getConditions()
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$search->setSlice(0, 1);
		$results = $this->_object->searchItems( $search, array(), $total );
		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 2, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$conditions = array(
			$search->compare( '==', 'service.type.code', 'delivery' ),
			$search->compare( '==', 'service.type.editor', $this->_editor )
		);
		$search->setConditions( $search->combine( '&&', $conditions ) );
		$results = $this->_object->searchItems( $search );

		if( ( $expected = reset( $results ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$actual = $this->_object->getItem( $expected->getId() );
		$this->assertEquals( $expected, $actual );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'service.type.editor', $this->_editor ) );
		$results = $this->_object->searchItems( $search );

		if( ( $item = reset($results) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$item->setId( null );
		$item->setCode( 'unitTestSave' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode( 'unitTestSave2' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $itemSaved->getId() );


		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getDomain(), $itemSaved->getDomain() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $this->_editor, $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getDomain(), $itemUpd->getDomain() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $this->_editor, $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $itemSaved->getId() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'unknown' );
	}
}

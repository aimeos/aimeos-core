<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MAdmin_Log_Manager_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MAdmin_Log_Manager_DefaultTest');
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
		$this->_object = new MAdmin_Log_Manager_Default( TestHelper::getContext() );
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
		$this->assertInstanceOf( 'MAdmin_Log_Item_Interface', $this->_object->createItem() );
	}


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attr ) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $attr );
		}
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MAdmin_Exception');
		$this->_object->getSubManager( 'unknown' );
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'log.id', null );
		$expr[] = $search->compare( '!=', 'log.siteid', null);
		$expr[] = $search->compare( '==', 'log.facility', 'unittest facility' );
		$expr[] = $search->compare( '>=', 'log.timestamp', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'log.priority', 1 );
		$expr[] = $search->compare( '==', 'log.message', 'unittest message' );
		$expr[] = $search->compare( '==', 'log.request', 'unittest request' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals(1, count($results));
		$this->assertEquals(1, $total);

		foreach($results as $itemId => $item) {
			$this->assertEquals($itemId, $item->getId());
		}
	}


	public function testGetItem()
	{
		$criteria = $this->_object->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'log.priority', 1 ) );
		$result = $this->_object->searchItems( $criteria );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$item->setMessage( 'unit test message' );
		$item->setRequest( 'unit test rqst' );
		$this->_object->saveItem( $item );

		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setRequest( 'unit test request' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $item->getId() );

		$this->_object->deleteItem( $item->getId() );

		$this->assertTrue( $item->getId() !== null );
		$this->assertTrue( $item->getTimestamp() === null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getFacility(), $itemSaved->getFacility() );
		$this->assertEquals( $item->getMessage(), $itemSaved->getMessage() );
		$this->assertEquals( $item->getRequest(), $itemSaved->getRequest() );
		$this->assertEquals( $item->getPriority(), $itemSaved->getPriority() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getFacility(), $itemUpd->getFacility() );
		$this->assertEquals( $itemExp->getMessage(), $itemUpd->getMessage() );
		$this->assertEquals( $itemExp->getRequest(), $itemUpd->getRequest() );
		$this->assertEquals( $itemExp->getPriority(), $itemUpd->getPriority() );

		$this->setExpectedException( 'MAdmin_Log_Exception' );
		$this->_object->getItem( $item->getId() );
	}
}

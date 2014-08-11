<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class MAdmin_Job_Manager_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = new MAdmin_Job_Manager_Default( TestHelper::getContext() );
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
		$this->assertInstanceOf( 'MAdmin_Job_Item_Interface', $this->_object->createItem() );
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
		$expr[] = $search->compare( '!=', 'job.id', null );
		$expr[] = $search->compare( '!=', 'job.siteid', null);
		$expr[] = $search->compare( '==', 'job.label', 'unittest job' );
		$expr[] = $search->compare( '==', 'job.method', 'controller.method' );
		$expr[] = $search->compare( '==', 'job.parameter', '{"items":"testfile.ext"}' );
		$expr[] = $search->compare( '==', 'job.result', '{"items":"testfile.ext"}' );
		$expr[] = $search->compare( '==', 'job.status', 0 );
		$expr[] = $search->compare( '>=', 'job.ctime', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'job.mtime', '2010-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'job.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		foreach($results as $itemId => $item) {
			$this->assertEquals( $itemId, $item->getId() );
		}
	}


	public function testGetItem()
	{
		$criteria = $this->_object->createSearch();
		$criteria->setConditions( $criteria->compare( '==', 'job.method', 'controller.method' ) );
		$result = $this->_object->searchItems( $criteria );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No item found' );
		}

		$this->assertEquals( $item, $this->_object->getItem( $item->getId() ) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$item = $this->_object->createItem();
		$item->setLabel( 'unit test' );
		$item->setMethod( 'crtl.method' );
		$this->_object->saveItem( $item );

		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setMethod( 'controll.method' );
		$this->_object->saveItem( $itemExp );
		$itemUpd = $this->_object->getItem( $item->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getResult(), $itemSaved->getResult() );
		$this->assertEquals( $item->getMethod(), $itemSaved->getMethod() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemSaved->getId() );
		$this->assertEquals( $itemExp->getSiteid(), $itemSaved->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getResult(), $itemUpd->getResult() );
		$this->assertEquals( $itemExp->getMethod(), $itemUpd->getMethod() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MAdmin_Job_Exception' );
		$this->_object->getItem( $item->getId() );
	}
}

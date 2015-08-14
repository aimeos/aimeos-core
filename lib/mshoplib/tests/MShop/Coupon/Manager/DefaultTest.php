<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Coupon_Manager_Default.
 */
class MShop_Coupon_Manager_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_item;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_object = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() );

		$this->_item = $this->_object->createItem();
		$this->_item->setProvider( 'Example' );
		$this->_item->setConfig( array( 'key'=>'value' ) );
		$this->_item->setStatus( '1' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_object );
	}


	public function testCleanup()
	{
		$this->_object->cleanup( array( -1 ) );
	}


	public function testGetSearchAttributes()
	{
		$attribs = $this->_object->getSearchAttributes();
		foreach( $attribs as $obj ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Coupon_Item_Interface', $this->_object->createItem() );
	}


	public function testGetSubManager()
	{
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager( 'code' ) );
		$this->assertInstanceOf( 'MShop_Common_Manager_Interface', $this->_object->getSubManager( 'code', 'Default' ) );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'unknown' );
	}


	public function testGetSubManagerInvalidManager()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( '$%^' );
	}


	public function testGetSubManagerInvalidName()
	{
		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getSubManager( 'Code', 'unknown' );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $itemA = reset( $results ) ) === false ) {
			throw new Exception( 'No results available' );
		}

		$itemB = $this->_object->getItem( $itemA->getId() );
		$this->assertEquals( 'Unit test example', $itemB->getLabel() );
	}

	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$result = $this->_object->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new Exception( 'No coupon item found' );
		}

		$item->setId( null );
		$item->setProvider( 'Unit' );
		$item->setConfig( array( 'key'=>'value' ) );
		$item->setStatus( '1' );
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setStatus( '0' );
		$this->_object->saveItem( $itemExp );

		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getLabel(), $itemSaved->getLabel() );
		$this->assertEquals( $item->getProvider(), $itemSaved->getProvider() );
		$this->assertEquals( $item->getConfig(), $itemSaved->getConfig() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );
		$this->assertEquals( $item->getStatus(), $itemSaved->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getLabel(), $itemUpd->getLabel() );
		$this->assertEquals( $itemExp->getProvider(), $itemUpd->getProvider() );
		$this->assertEquals( $itemExp->getConfig(), $itemUpd->getConfig() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );
		$this->assertEquals( $itemExp->getStatus(), $itemUpd->getStatus() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getItem( $item->getId() );
	}


	public function testGetProvider()
	{
		$item = $this->_object->createItem();
		$item->setProvider( 'Present,Example' );
		$provider = $this->_object->getProvider( $item, 'abcd' );

		$this->assertInstanceOf( 'MShop_Coupon_Provider_Interface', $provider );
		$this->assertInstanceOf( 'MShop_Coupon_Provider_Decorator_Example', $provider );


		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->getProvider( $this->_object->createItem(), '' );
	}


	public function testCreateSearch()
	{
		$search = $this->_object->createSearch();
		$this->assertInstanceOf( 'MW_Common_Criteria_SQL', $search );
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr = array();
		$expr[] = $search->compare( '!=', 'coupon.id', null );
		$expr[] = $search->compare( '!=', 'coupon.siteid', null );
		$expr[] = $search->compare( '==', 'coupon.label', 'Unit test fixed rebate' );
		$expr[] = $search->compare( '=~', 'coupon.provider', 'FixedRebate' );
		$expr[] = $search->compare( '~=', 'coupon.config', 'product' );
		$expr[] = $search->compare( '==', 'coupon.datestart', '2002-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.dateend', '2100-12-31 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.status', 1 );
		$expr[] = $search->compare( '>=', 'coupon.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.editor', '' );

		$expr[] = $search->compare( '!=', 'coupon.code.id', null );
		$expr[] = $search->compare( '!=', 'coupon.code.siteid', null );
		$expr[] = $search->compare( '!=', 'coupon.code.couponid', null );
		$expr[] = $search->compare( '==', 'coupon.code.code', '5678' );
		$expr[] = $search->compare( '>=', 'coupon.code.count', 0 );
		$expr[] = $search->compare( '==', 'coupon.code.datestart', '2000-01-01 00:00:00' );
		$expr[] = $search->compare( '==', 'coupon.code.dateend', '2004-12-21 23:59:59' );
		$expr[] = $search->compare( '>=', 'coupon.code.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );


		//search with base criteria
		$search = $this->_object->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'coupon.code.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 5, count( $this->_object->searchItems( $search ) ) );
	}
}

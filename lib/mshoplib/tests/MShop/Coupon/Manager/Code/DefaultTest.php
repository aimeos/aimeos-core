<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Coupon_Manager_Default.
 */
class MShop_Coupon_Manager_Code_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_code;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Coupon_Manager_DefaultTest');
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
		$couponManager = MShop_Coupon_Manager_Factory::createManager( TestHelper::getContext() );

		$search = $couponManager->createSearch();
		$search->setConditions( $search->compare( '~=', 'coupon.code.code', 'OPQR') );
		$results = $couponManager->searchItems( $search );

		if( ( $item = reset( $results ) ) === false ) {
			throw new Exception( 'Code item not found' );
		};

		$this->_object = $couponManager->getSubManager( 'code' );
		$this->_code = $this->_object->createItem();
		$this->_code->setCode( 'abcd' );
		$this->_code->setCount( '1' );
		$this->_code->setCouponId( $item->getId() );

	}


	protected function tearDown()
	{
		unset($this->_object, $this->_code);
		MShop_Factory::clear();
	}


	public function testGetSearchAttributes()
	{
		foreach($this->_object->getSearchAttributes() AS $obj) {
			$this->assertInstanceOf('MW_Common_Criteria_Attribute_Interface', $obj );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Coupon_Item_Code_Interface', $this->_object->createItem() );
	}


	public function testGetItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->_object->searchItems( $search );

		if ( ( $codeItem = reset( $results ) ) === false) {
			throw new Exception( 'no item found exception' );
		}

		$item = $this->_object->getItem( $codeItem->getId() );
		$this->assertEquals( $codeItem, $item );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$result = $this->_object->searchItems($search);

		if ( ( $item = reset($result) ) === false ) {
			throw new Exception('No coupon code item found');
		}

		$item->setId(null);
		$item->setCode('unittest');
		$this->_object->saveItem( $item );
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;

		$itemExp->setCount( '231199' );
		$this->_object->saveItem( $itemExp );

		$itemUpd = $this->_object->getItem( $itemExp->getId() );

		$this->_object->deleteItem( $item->getId() );

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId() );
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getCouponId(), $itemSaved->getCouponId() );
		$this->assertEquals( $item->getCode(), $itemSaved->getCode() );
		$this->assertEquals( $item->getCount(), $itemSaved->getCount() );
		$this->assertEquals( $item->getDateStart(), $itemSaved->getDateStart() );
		$this->assertEquals( $item->getDateEnd(), $itemSaved->getDateEnd() );

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId() );
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getCouponId(), $itemUpd->getCouponId() );
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode() );
		$this->assertEquals( $itemExp->getCount(), $itemUpd->getCount() );
		$this->assertEquals( $itemExp->getDateStart(), $itemUpd->getDateStart() );
		$this->assertEquals( $itemExp->getDateEnd(), $itemUpd->getDateEnd() );

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem( $item->getId() );
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf('MW_Common_Criteria_SQL', $this->_object->createSearch());
	}


	public function testSearchItems()
	{
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'coupon.code.id', null );
		$expr[] = $search->compare( '!=', 'coupon.code.siteid', null );
		$expr[] = $search->compare( '!=', 'coupon.code.couponid', null );
		$expr[] = $search->compare( '==', 'coupon.code.code', 'OPQR' );
		$expr[] = $search->compare( '==', 'coupon.code.count', 2000000 );
		$expr[] = $search->compare( '==', 'coupon.code.datestart', null );
		$expr[] = $search->compare( '==', 'coupon.code.dateend', null );
		$expr[] = $search->compare( '>=', 'coupon.code.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'coupon.code.editor', '' );

		$total = 0;
		$search->setConditions( $search->combine( '&&', $expr ) );
		$results = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 1, count( $results ) );
		$this->assertEquals( 1, $total );

		//search with base criteria
		$search = $this->_object->createSearch(true);
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'coupon.code.editor', 'core:unittest' ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
		$this->assertEquals( 5, count( $this->_object->searchItems( $search ) ) );
	}


	public function testDecrease()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new Exception( 'No coupon code item found.' );
		}

		$this->_object->decrease( $codeItem->getCode(), 1 );
		$actual = $this->_object->getItem( $codeItem->getId() );
		$this->_object->increase( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() - 1, $actual->getCount() );
	}


	public function testIncrease()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '==', 'coupon.code.code', 'OPQR' ) );
		$results = $this->_object->searchItems( $search );

		if( ( $codeItem = reset( $results ) ) === false ) {
			throw new Exception( 'No coupon code item found.' );
		}

		$this->_object->increase( $codeItem->getCode(), 1 );
		$actual = $this->_object->getItem( $codeItem->getId() );
		$this->_object->decrease( $codeItem->getCode(), 1 );

		$this->assertEquals( $codeItem->getCount() + 1, $actual->getCount() );
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}

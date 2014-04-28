<?php

/**
 * @version $Id: DefaultTest.php 85 2012-10-01 16:25:09Z doleiynyk $
 */


/**
 * Test class for MShop_Order_Manager_Base_Coupon_Default.
 */
class MShop_Order_Manager_Base_Coupon_DefaultTest extends MW_Unittest_Testcase
{
	private $_context;
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

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Manager_Base_Coupon_DefaultTest');
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
		$this->_context = TestHelper::getContext();
		$this->_object = new MShop_Order_Manager_Base_Coupon_Default($this->_context);
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


	public function testGetSearchAttributes()
	{
		foreach( $this->_object->getSearchAttributes() as $attribute ) {
			$this->assertInstanceOf( 'MW_Common_Criteria_Attribute_Interface', $attribute );
		}
	}


	public function testCreateItem()
	{
		$this->assertInstanceOf( 'MShop_Order_Item_Base_Coupon_Interface', $this->_object->createItem());
	}


	public function testCreateSearch()
	{
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch());
		$this->assertInstanceOf( 'MW_Common_Criteria_Interface', $this->_object->createSearch(true));
	}


	public function testSearchItem()
	{
		$siteid = $this->_context->getLocale()->getSiteId();

		$total = 0;
		$search = $this->_object->createSearch();

		$expr[] = $search->compare( '!=', 'order.base.coupon.id', null );
		$expr[] = $search->compare( '==', 'order.base.coupon.siteid', $siteid );
		$expr[] = $search->compare( '!=', 'order.base.coupon.baseid', null );
		$expr[] = $search->compare( '!=', 'order.base.coupon.productid', null );
		$expr[] = $search->compare( '==', 'order.base.coupon.code', 'OPQR' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.mtime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.ctime', '1970-01-01 00:00:00' );
		$expr[] = $search->compare( '>=', 'order.base.coupon.editor', '' );

		$search->setConditions( $search->combine( '&&', $expr ) );
		$result = $this->_object->searchItems( $search, array(), $total );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( 2, $total );


		$search->setConditions( $search->compare( '==', 'order.base.coupon.code', array('OPQR','5678')));
		$search->setSlice( 0, 1 );

		$total = 0;
		$results = $this->_object->searchItems( $search, array(), $total);

		$this->assertEquals( 1, count( $results ) );
		$this->assertGreaterThanOrEqual( 4, $total );
	}


	public function testGetItem()
	{
		$obj = $this->_object;
		$search = $obj->createSearch();

		$search->setConditions( $search->compare( '==', 'order.base.coupon.code', 'OPQR') );
		$results = $obj->searchItems( $search );

		if ( ($item = reset($results) ) === false ) {
			throw new Exception('empty results');
		}

		$this->assertEquals( $item, $obj->getItem($item->getId()) );
	}


	public function testSaveUpdateDeleteItem()
	{
		$search = $this->_object->createSearch();
		$search->setConditions( $search->compare( '>=', 'order.base.coupon.productid', '1') );
		$results = $this->_object->searchItems( $search );

		if ( !($item = reset($results)) ) {
			throw new Exception('empty results');
		}

		$item->setId(null);
		$this->_object->saveItem($item);
		$itemSaved = $this->_object->getItem( $item->getId() );

		$itemExp = clone $itemSaved;
		$itemExp->setCode('unitUpdCode');
		$this->_object->saveItem($itemExp);
		$itemUpd = $this->_object->getItem($item->getId());

		$this->_object->deleteItem($item->getId());

		$context = TestHelper::getContext();

		$this->assertTrue( $item->getId() !== null );
		$this->assertEquals( $item->getId(), $itemSaved->getId());
		$this->assertEquals( $item->getSiteId(), $itemSaved->getSiteId() );
		$this->assertEquals( $item->getBaseId(), $itemSaved->getBaseId());
		$this->assertEquals( $item->getCode(), $itemSaved->getCode());
		$this->assertEquals( $item->getProductId(), $itemSaved->getProductId());

		$this->assertEquals( $context->getEditor(), $itemSaved->getEditor() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemSaved->getTimeModified() );

		$this->assertEquals( $itemExp->getId(), $itemUpd->getId());
		$this->assertEquals( $itemExp->getSiteId(), $itemUpd->getSiteId() );
		$this->assertEquals( $itemExp->getBaseId(), $itemUpd->getBaseId());
		$this->assertEquals( $itemExp->getCode(), $itemUpd->getCode());
		$this->assertEquals( $itemExp->getProductId(), $itemUpd->getProductId());

		$this->assertEquals( $context->getEditor(), $itemUpd->getEditor() );
		$this->assertEquals( $itemExp->getTimeCreated(), $itemUpd->getTimeCreated() );
		$this->assertRegExp( '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $itemUpd->getTimeModified() );

		$this->setExpectedException('MShop_Exception');
		$this->_object->getItem($item->getId());
	}


	public function testGetSubManager()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->getSubManager('unknown');
	}
}

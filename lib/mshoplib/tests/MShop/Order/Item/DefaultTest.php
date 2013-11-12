<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Order_Item_Default.
 */
class MShop_Order_Item_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MShop_Order_Item_Default
	 * @access protected
	 */
	private $_object;
	private $_values;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Item_DefaultTest');
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
		$this->_values = array(
			'id' => 15,
			'siteid'=>99,
			'type' => MShop_Order_Item_Abstract::TYPE_WEB,
			'statusdelivery' => MShop_Order_Item_Abstract::STAT_PENDING,
			'statuspayment' => MShop_Order_Item_Abstract::PAY_RECEIVED,
			'datepayment' => '2004-12-01 12:34:56',
			'datedelivery' => '2004-01-03 12:34:56',
			'relatedid' => 1,
			'baseid' => 4,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Order_Item_Default($this->_values);
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

	public function testGetId()
	{
		$this->assertEquals($this->_values['id'], $this->_object->getId());
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals(null, $this->_object->getId() );
		$this->assertTrue($this->_object->isModified());

		$this->_object->setId(15);
		$this->assertEquals(15, $this->_object->getId() );
		$this->assertFalse($this->_object->isModified());

		$this->setExpectedException('MShop_Exception');
		$this->_object->setId(6);
	}

	public function testSetId2()
	{
		$this->setExpectedException('MShop_Exception');
		$this->_object->setId('test');
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetBaseId()
	{
		$this->assertSame($this->_values['baseid'], $this->_object->getBaseId());
	}

	public function testSetBaseId()
	{
		$this->_object->setBaseId( 15 );
		$this->assertEquals( 15, $this->_object->getBaseId());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetType()
	{
		$this->assertEquals($this->_values['type'], $this->_object->getType());
	}

	public function testSetType()
	{
		$this->_object->setType(MShop_Order_Item_Abstract::TYPE_PHONE);
		$this->assertEquals(MShop_Order_Item_Abstract::TYPE_PHONE, $this->_object->getType());
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Order_Exception');
		$this->_object->setType(500);
	}

	public function testGetDateDelivery()
	{
		$this->assertEquals( $this->_values['datedelivery'], $this->_object->getDateDelivery() );
	}

	public function testSetDateDelivery()
	{
		$this->_object->setDateDelivery('2008-04-12 12:34:56');
		$this->assertEquals('2008-04-12 12:34:56', $this->_object->getDateDelivery() );
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Order_Exception');
		$this->_object->setDateDelivery('2008-34-12');
	}

	public function testGetDatePayment()
	{
		$this->assertEquals( $this->_values['datepayment'], $this->_object->getDatePayment() );
	}

	public function testSetDatePayment()
	{
		$this->_object->setDatePayment('2008-04-12 12:34:56');
		$this->assertEquals('2008-04-12 12:34:56', $this->_object->getDatePayment() );
		$this->assertTrue($this->_object->isModified());

		$this->setExpectedException('MShop_Order_Exception');
		$this->_object->setDatePayment('2008-34-12');
	}

	public function testGetDeliveryStatus()
	{
		$this->assertEquals($this->_values['statusdelivery'], $this->_object->getDeliveryStatus() );
	}

	public function testSetDeliveryStatus()
	{
		$this->_object->setDeliveryStatus(MShop_Order_Item_Abstract::STAT_PROGRESS);
		$this->assertEquals(MShop_Order_Item_Abstract::STAT_PROGRESS, $this->_object->getDeliveryStatus() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetPaymentStatus()
	{
		$this->assertEquals($this->_values['statuspayment'], $this->_object->getPaymentStatus() );
	}

	public function testSetPaymentStatus()
	{
		$this->_object->setPaymentStatus(MShop_Order_Item_Abstract::PAY_DELETED);
		$this->assertEquals(MShop_Order_Item_Abstract::PAY_DELETED, $this->_object->getPaymentStatus() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetRelatedId()
	{
		$this->assertEquals($this->_values['relatedid'], $this->_object->getRelatedId() );
	}

	public function testSetRelatedId()
	{
		$this->_object->setRelatedId( 22 );
		$this->assertEquals(22, $this->_object->getRelatedId() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->_object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}

	public function testToArray()
	{
		$list = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( $this->_object->getId(), $list['order.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['order.siteid'] );
		$this->assertEquals( $this->_object->getType(), $list['order.type'] );
		$this->assertEquals( $this->_object->getDeliveryStatus(), $list['order.statusdelivery'] );
		$this->assertEquals( $this->_object->getPaymentStatus(), $list['order.statuspayment'] );
		$this->assertEquals( $this->_object->getDatePayment(), $list['order.datepayment'] );
		$this->assertEquals( $this->_object->getDateDelivery(), $list['order.datedelivery'] );
		$this->assertEquals( $this->_object->getBaseId(), $list['order.baseid'] );
		$this->assertEquals( $this->_object->getRelatedId(), $list['order.relatedid'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['order.ctime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['order.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}

	public function testMagicGetOldPaymentStatus()
	{
		$this->assertEquals( MShop_Order_Item_Abstract::PAY_RECEIVED, $this->_object->oldPaymentStatus );
	}

	public function testMagicGetOldDeliveryStatus()
	{
		$this->assertEquals( MShop_Order_Item_Abstract::STAT_PENDING, $this->_object->oldDeliveryStatus );
	}

	public function testMagicGetException()
	{
		$this->setExpectedException( 'MShop_Order_Exception' );
		$this->_object->notExisting;
	}
}

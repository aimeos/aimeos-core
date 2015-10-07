<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


namespace Aimeos\MShop\Order\Item;


/**
 * Test class for \Aimeos\MShop\Order\Item\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->values = array(
			'id' => 15,
			'siteid'=>99,
			'type' => \Aimeos\MShop\Order\Item\Base::TYPE_WEB,
			'statusdelivery' => \Aimeos\MShop\Order\Item\Base::STAT_PENDING,
			'statuspayment' => \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
			'datepayment' => '2004-12-01 12:34:56',
			'datedelivery' => '2004-01-03 12:34:56',
			'relatedid' => 1,
			'baseid' => 4,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Standard( $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
	}

	public function testGetId()
	{
		$this->assertEquals( $this->values['id'], $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 15 );
		$this->assertEquals( 15, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 6 );
	}

	public function testSetId2()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 'test' );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetBaseId()
	{
		$this->assertSame( $this->values['baseid'], $this->object->getBaseId() );
	}

	public function testSetBaseId()
	{
		$this->object->setBaseId( 15 );
		$this->assertEquals( 15, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetType()
	{
		$this->assertEquals( $this->values['type'], $this->object->getType() );
	}

	public function testSetType()
	{
		$this->object->setType( \Aimeos\MShop\Order\Item\Base::TYPE_PHONE );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::TYPE_PHONE, $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setType( 500 );
	}

	public function testGetDateDelivery()
	{
		$this->assertEquals( $this->values['datedelivery'], $this->object->getDateDelivery() );
	}

	public function testSetDateDelivery()
	{
		$this->object->setDateDelivery( '2008-04-12 12:34:56' );
		$this->assertEquals( '2008-04-12 12:34:56', $this->object->getDateDelivery() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setDateDelivery( '2008-34-12' );
	}

	public function testGetDatePayment()
	{
		$this->assertEquals( $this->values['datepayment'], $this->object->getDatePayment() );
	}

	public function testSetDatePayment()
	{
		$this->object->setDatePayment( '2008-04-12 12:34:56' );
		$this->assertEquals( '2008-04-12 12:34:56', $this->object->getDatePayment() );
		$this->assertTrue( $this->object->isModified() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->setDatePayment( '2008-34-12' );
	}

	public function testGetDeliveryStatus()
	{
		$this->assertEquals( $this->values['statusdelivery'], $this->object->getDeliveryStatus() );
	}

	public function testSetDeliveryStatus()
	{
		$this->object->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $this->object->getDeliveryStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetPaymentStatus()
	{
		$this->assertEquals( $this->values['statuspayment'], $this->object->getPaymentStatus() );
	}

	public function testSetPaymentStatus()
	{
		$this->object->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_DELETED, $this->object->getPaymentStatus() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetRelatedId()
	{
		$this->assertEquals( $this->values['relatedid'], $this->object->getRelatedId() );
	}

	public function testSetRelatedId()
	{
		$this->object->setRelatedId( 22 );
		$this->assertEquals( 22, $this->object->getRelatedId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2011-01-01 00:00:02', $this->object->getTimeModified() );
	}

	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}

	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Standard();

		$list = array(
			'order.id' => 1,
			'order.type' => \Aimeos\MShop\Order\Item\Base::TYPE_WEB,
			'order.baseid' => 2,
			'order.relatedid' => 3,
			'order.statusdelivery' => 4,
			'order.statuspayment' => 5,
			'order.datepayment' => '2000-01-01 00:00:00',
			'order.datedelivery' => '2001-01-01 00:00:00',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['order.id'], $item->getId() );
		$this->assertEquals( $list['order.type'], $item->getType() );
		$this->assertEquals( $list['order.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.relatedid'], $item->getRelatedId() );
		$this->assertEquals( $list['order.statusdelivery'], $item->getDeliveryStatus() );
		$this->assertEquals( $list['order.statuspayment'], $item->getPaymentStatus() );
		$this->assertEquals( $list['order.datepayment'], $item->getDatePayment() );
		$this->assertEquals( $list['order.datedelivery'], $item->getDateDelivery() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.siteid'] );
		$this->assertEquals( $this->object->getType(), $list['order.type'] );
		$this->assertEquals( $this->object->getDeliveryStatus(), $list['order.statusdelivery'] );
		$this->assertEquals( $this->object->getPaymentStatus(), $list['order.statuspayment'] );
		$this->assertEquals( $this->object->getDatePayment(), $list['order.datepayment'] );
		$this->assertEquals( $this->object->getDateDelivery(), $list['order.datedelivery'] );
		$this->assertEquals( $this->object->getBaseId(), $list['order.baseid'] );
		$this->assertEquals( $this->object->getRelatedId(), $list['order.relatedid'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.ctime'] );
		$this->assertEquals( $this->object->getEditor(), $list['order.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}

	public function testMagicGetOldPaymentStatus()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED, $this->object->oldPaymentStatus );
	}

	public function testMagicGetOldDeliveryStatus()
	{
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PENDING, $this->object->oldDeliveryStatus );
	}

	public function testMagicGetException()
	{
		$this->setExpectedException( '\\Aimeos\\MShop\\Order\\Exception' );
		$this->object->notExisting;
	}
}

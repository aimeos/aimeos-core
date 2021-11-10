<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.id' => 15,
			'order.siteid' => 99,
			'order.type' => \Aimeos\MShop\Order\Item\Base::TYPE_WEB,
			'order.statusdelivery' => \Aimeos\MShop\Order\Item\Base::STAT_PENDING,
			'order.statuspayment' => \Aimeos\MShop\Order\Item\Base::PAY_RECEIVED,
			'order.datepayment' => '2004-12-01 12:34:56',
			'order.datedelivery' => '2004-01-03 12:34:56',
			'order.relatedid' => '123',
			'order.baseid' => 4,
			'order.mtime' => '2011-01-01 00:00:02',
			'order.ctime' => '2011-01-01 00:00:01',
			'order.editor' => 'unitTestUser'
		);

		$baseItem = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'order/base' )->create();
		$this->object = new \Aimeos\MShop\Order\Item\Standard( $this->values, $baseItem );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetBaseItem()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $this->object->getBaseItem() );
		$this->assertNull( ( new \Aimeos\MShop\Order\Item\Standard( $this->values ) )->getBaseItem() );
	}


	public function testSetBaseItem()
	{
		$item = new \Aimeos\MShop\Order\Item\Standard( $this->values );
		$baseItem = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'order/base' )->create();

		$result = $this->object->setBaseItem( $baseItem );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $result );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $result->getBaseItem() );
	}


	public function testGetId()
	{
		$this->assertEquals( $this->values['order.id'], $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 15 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( 15, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetOrderNumber()
	{
		$this->assertEquals( $this->values['order.id'], $this->object->getOrderNumber() );
	}


	public function testGetOrderNumberCustom()
	{
		\Aimeos\MShop\Order\Item\Base::macro( 'ordernumber', function( \Aimeos\MShop\Order\Item\Iface $item ) {
			return 'order-' . $item->getId() . 'Z';
		} );

		$this->assertEquals( 'order-' . $this->values['order.id'] . 'Z', $this->object->getOrderNumber() );

		\Aimeos\MShop\Order\Item\Base::macro( 'ordernumber', function( \Aimeos\MShop\Order\Item\Iface $item ) {
			return $item->getId();
		} );
	}


	public function testGetBaseId()
	{
		$this->assertEquals( $this->values['order.baseid'], $this->object->getBaseId() );
	}


	public function testSetBaseId()
	{
		$return = $this->object->setBaseId( 15 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( 15, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( $this->values['order.type'], $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( \Aimeos\MShop\Order\Item\Base::TYPE_PHONE );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::TYPE_PHONE, $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetDateDelivery()
	{
		$this->assertEquals( $this->values['order.datedelivery'], $this->object->getDateDelivery() );
	}


	public function testSetDateDelivery()
	{
		$return = $this->object->setDateDelivery( '2008-04-12 12:34:56' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( '2008-04-12 12:34:56', $this->object->getDateDelivery() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setDateDelivery( '2008-34-12' );
	}


	public function testGetDatePayment()
	{
		$this->assertEquals( $this->values['order.datepayment'], $this->object->getDatePayment() );
	}


	public function testSetDatePayment()
	{
		$return = $this->object->setDatePayment( '2008-04-12 12:34:56' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( '2008-04-12 12:34:56', $this->object->getDatePayment() );
		$this->assertTrue( $this->object->isModified() );

		$this->expectException( \Aimeos\MShop\Exception::class );
		$this->object->setDatePayment( '2008-34-12' );
	}


	public function testGetStatusDelivery()
	{
		$this->assertEquals( $this->values['order.statusdelivery'], $this->object->getStatusDelivery() );
	}


	public function testSetStatusDelivery()
	{
		$return = $this->object->setStatusDelivery( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS, $this->object->getStatusDelivery() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetStatusDeliveryNull()
	{
		$return = $this->object->setStatusDelivery( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertNull( $this->object->getStatusDelivery() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetStatusPayment()
	{
		$this->assertEquals( $this->values['order.statuspayment'], $this->object->getStatusPayment() );
	}


	public function testSetStatusPayment()
	{
		$return = $this->object->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( \Aimeos\MShop\Order\Item\Base::PAY_DELETED, $this->object->getStatusPayment() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testSetStatusPaymentNull()
	{
		$return = $this->object->setStatusPayment( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertNull( $this->object->getStatusPayment() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetRelatedId()
	{
		$this->assertEquals( $this->values['order.relatedid'], $this->object->getRelatedId() );
	}


	public function testSetRelatedId()
	{
		$return = $this->object->setRelatedId( 22 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $return );
		$this->assertEquals( '22', $this->object->getRelatedId() );
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


	public function testGetResourceType()
	{
		$this->assertEquals( 'order', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Standard();

		$list = $entries = array(
			'order.id' => 1,
			'order.type' => \Aimeos\MShop\Order\Item\Base::TYPE_WEB,
			'order.baseid' => 2,
			'order.relatedid' => '3',
			'order.statusdelivery' => 4,
			'order.statuspayment' => 5,
			'order.datepayment' => '2000-01-01 00:00:00',
			'order.datedelivery' => '2001-01-01 00:00:00',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.id'], $item->getId() );
		$this->assertEquals( $list['order.type'], $item->getType() );
		$this->assertEquals( $list['order.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.relatedid'], $item->getRelatedId() );
		$this->assertEquals( $list['order.statusdelivery'], $item->getStatusDelivery() );
		$this->assertEquals( $list['order.statuspayment'], $item->getStatusPayment() );
		$this->assertEquals( $list['order.datepayment'], $item->getDatePayment() );
		$this->assertEquals( $list['order.datedelivery'], $item->getDateDelivery() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );
		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.siteid'] );
		$this->assertEquals( $this->object->getType(), $list['order.type'] );
		$this->assertEquals( $this->object->getStatusDelivery(), $list['order.statusdelivery'] );
		$this->assertEquals( $this->object->getStatusPayment(), $list['order.statuspayment'] );
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
}

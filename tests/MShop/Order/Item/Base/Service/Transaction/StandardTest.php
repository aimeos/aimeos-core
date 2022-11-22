<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */


namespace Aimeos\MShop\Order\Item\Base\Service\Transaction;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;
	private $price;


	protected function setUp() : void
	{
		$this->values = array(
			'order.base.service.transaction.id' => 3,
			'order.base.service.transaction.siteid' => 99,
			'order.base.service.transaction.parentid' => 42,
			'order.base.service.transaction.type' => 'payment',
			'order.base.service.transaction.currencyid' => 'EUR',
			'order.base.service.transaction.price' => '9.00',
			'order.base.service.transaction.costs' => '1.00',
			'order.base.service.transaction.rebate' => '3.00',
			'order.base.service.transaction.taxvalue' => '2.000',
			'order.base.service.transaction.taxflag' => true,
			'order.base.service.transaction.config' => ['tx' => 123],
			'order.base.service.transaction.status' => -1,
			'order.base.service.transaction.mtime' => '2020-12-31 23:59:59',
			'order.base.service.transaction.ctime' => '2011-01-01 00:00:01',
			'order.base.service.transaction.editor' => 'unitTestUser'
		);

		$this->price = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create()->setValue( '100' );
		$this->object = new \Aimeos\MShop\Order\Item\Base\Service\Transaction\Standard( $this->price, $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( 3, $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 99 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( 99, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testSetSiteId()
	{
		$this->object->setSiteId( 100 );
		$this->assertEquals( 100, $this->object->getSiteId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetParentId()
	{
		$this->assertEquals( 42, $this->object->getParentId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetParentId()
	{
		$return = $this->object->setParentId( 98 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( 98, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( 'payment', $this->object->getType() );
	}


	public function testSetType()
	{
		$return = $this->object->setType( 'refund' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( 'refund', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetPrice()
	{
		$this->assertEquals( $this->price, $this->object->getPrice() );
	}


	public function testSetPrice()
	{
		$price = \Aimeos\MShop::create( \TestHelper::context(), 'price' )->create()->setValue( '1.00' );
		$return = $this->object->setPrice( $price );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( $price, $this->object->getPrice() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetConfig()
	{
		$this->assertEquals( ['tx' => 123], $this->object->getConfig() );
	}


	public function testSetConfig()
	{
		$return = $this->object->setConfig( ['ref' => 321] );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Service\Transaction\Iface::class, $return );
		$this->assertEquals( ['ref' => 321], $this->object->getConfig() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2020-12-31 23:59:59', $this->object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base/service/transaction', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Service\Transaction\Standard( $this->price );

		$list = $entries = [
			'order.base.service.transaction.id' => 1,
			'order.base.service.transaction.parentid' => 3,
			'order.base.service.transaction.type' => 'payment',
			'order.base.service.transaction.currencyid' => 'USD',
			'order.base.service.transaction.price' => '9.00',
			'order.base.service.transaction.costs' => '1.00',
			'order.base.service.transaction.rebate' => '3.00',
			'order.base.service.transaction.taxvalue' => '2.0000',
			'order.base.service.transaction.taxflag' => true,
			'order.base.service.transaction.config' => ['tx' => 123],
			'order.base.service.transaction.status' => -1,
		];

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.base.service.transaction.id'], $item->getId() );
		$this->assertEquals( $list['order.base.service.transaction.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.base.service.transaction.type'], $item->getType() );
		$this->assertEquals( $list['order.base.service.transaction.currencyid'], $item->getPrice()->getCurrencyId() );
		$this->assertEquals( $list['order.base.service.transaction.price'], $item->getPrice()->getValue() );
		$this->assertEquals( $list['order.base.service.transaction.costs'], $item->getPrice()->getCosts() );
		$this->assertEquals( $list['order.base.service.transaction.rebate'], $item->getPrice()->getRebate() );
		$this->assertEquals( $list['order.base.service.transaction.taxvalue'], $item->getPrice()->getTaxvalue() );
		$this->assertEquals( $list['order.base.service.transaction.taxflag'], $item->getPrice()->getTaxflag() );
		$this->assertEquals( $list['order.base.service.transaction.config'], $item->getConfig() );
		$this->assertEquals( $list['order.base.service.transaction.status'], $item->getStatus() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.base.service.transaction.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.base.service.transaction.siteid'] );
		$this->assertEquals( $this->object->getParentId(), $list['order.base.service.transaction.parentid'] );
		$this->assertEquals( $this->object->getType(), $list['order.base.service.transaction.type'] );
		$this->assertEquals( $this->object->getPrice()->getCurrencyId(), $list['order.base.service.transaction.currencyid'] );
		$this->assertEquals( $this->object->getPrice()->getValue(), $list['order.base.service.transaction.price'] );
		$this->assertEquals( $this->object->getPrice()->getCosts(), $list['order.base.service.transaction.costs'] );
		$this->assertEquals( $this->object->getPrice()->getRebate(), $list['order.base.service.transaction.rebate'] );
		$this->assertEquals( $this->object->getPrice()->getTaxvalue(), $list['order.base.service.transaction.taxvalue'] );
		$this->assertEquals( $this->object->getPrice()->getTaxflag(), $list['order.base.service.transaction.taxflag'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.service.transaction.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.base.service.transaction.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.service.transaction.mtime'] );
		$this->assertEquals( $this->object->editor(), $list['order.base.service.transaction.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

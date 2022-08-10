<?php
/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 */

namespace Aimeos\MShop\Order\Item\Cart;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	protected function setUp() : void
	{
		$this->values = array(
			'order.cart.id' => '123-456-789',
			'order.cart.siteid' => '1.',
			'order.cart.customerid' => '11',
			'order.cart.name' => 'testcart',
			'order.cart.content' => 'this is a value from unittest',
			'order.cart.mtime' => '2011-01-01 00:00:02',
			'order.cart.ctime' => '2011-01-01 00:00:01',
			'order.cart.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Cart\Standard( $this->values );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetId()
	{
		$this->assertEquals( '123-456-789', $this->object->getId() );
	}


	public function testSetId()
	{
		$return = $this->object->setId( '987-654-321' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Cart\Iface::class, $return );
		$this->assertEquals( '987-654-321', $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( '1.', $this->object->getSiteId() );
	}


	public function testGetCustomerId()
	{
		$this->assertEquals( '11', $this->object->getCustomerId() );
	}


	public function testSetCustomerId()
	{
		$return = $this->object->setCustomerId( '12' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Cart\Iface::class, $return );
		$this->assertEquals( '12', $this->object->getCustomerId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetContent()
	{
		$this->assertEquals( "this is a value from unittest", $this->object->getContent() );
	}


	public function testSetContent()
	{
		$return = $this->object->setContent( 'was changed by unittest' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Cart\Iface::class, $return );
		$this->assertEquals( 'was changed by unittest', $this->object->getContent() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( 'testcart', $this->object->getName() );
	}


	public function testSetName()
	{
		$return = $this->object->setName( 'unittest' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Cart\Iface::class, $return );
		$this->assertEquals( 'unittest', $this->object->getName() );
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
		$this->assertEquals( 'unitTestUser', $this->object->editor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/cart', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Cart\Standard();

		$list = $entries = array(
			'order.cart.id' => '123-456',
			'order.cart.customerid' => '123',
			'order.cart.content' => 'cart value',
			'order.cart.name' => 'test cart name',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( '', $item->getSiteId() );
		$this->assertEquals( $list['order.cart.id'], $item->getId() );
		$this->assertEquals( $list['order.cart.customerid'], $item->getCustomerId() );
		$this->assertEquals( $list['order.cart.content'], $item->getContent() );
		$this->assertEquals( $list['order.cart.name'], $item->getName() );
	}


	public function testToArray()
	{
		$list = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.cart.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.cart.siteid'] );
		$this->assertEquals( $this->object->getCustomerId(), $list['order.cart.customerid'] );
		$this->assertEquals( $this->object->getContent(), $list['order.cart.content'] );
		$this->assertEquals( $this->object->getName(), $list['order.cart.name'] ); ;
		$this->assertEquals( $this->object->getTimeModified(), $list['order.cart.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.cart.ctime'] );
		$this->assertEquals( $this->object->editor(), $list['order.cart.editor'] );
	}
}

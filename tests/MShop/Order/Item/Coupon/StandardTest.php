<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Order\Item\Coupon;


/**
 * Test class for \Aimeos\MShop\Order\Item\Coupon\Standard.
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp() : void
	{
		$this->values = array(
			'order.coupon.id' => 1,
			'order.coupon.siteid' => 99,
			'order.coupon.parentid' => 42,
			'order.coupon.code' => 'SomeCode',
			'order.coupon.ordprodid' => 566778,
			'order.coupon.mtime' => '2001-12-30 23:59:59',
			'order.coupon.ctime' => '2011-01-01 00:00:01',
			'order.coupon.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Coupon\Standard( $this->values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown() : void
	{
		unset( $this->object );
	}

	public function testGetId()
	{
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$return = $this->object->setId( null );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Coupon\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Coupon\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetParentId()
	{
		$this->assertEquals( 42, $this->object->getParentId() );
	}

	public function testSetParentId()
	{
		$return = $this->object->setParentId( 99 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Coupon\Iface::class, $return );
		$this->assertEquals( 99, $this->object->getParentId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'SomeCode', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$return = $this->object->setCode( 'testId' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Coupon\Iface::class, $return );
		$this->assertEquals( 'testId', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( 566778, $this->object->getProductId() );
	}

	public function testSetProductId()
	{
		$return = $this->object->setProductId( 12345 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Coupon\Iface::class, $return );
		$this->assertEquals( 12345, $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( '2001-12-30 23:59:59', $this->object->getTimeModified() );
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
		$this->assertEquals( 'order/coupon', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Coupon\Standard();

		$list = $entries = array(
			'order.coupon.id' => 1,
			'order.coupon.parentid' => 2,
			'order.coupon.productid' => 3,
			'order.coupon.code' => 'test',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['order.coupon.id'], $item->getId() );
		$this->assertEquals( $list['order.coupon.parentid'], $item->getParentId() );
		$this->assertEquals( $list['order.coupon.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.coupon.code'], $item->getCode() );
	}


	public function testToArray()
	{
		$array = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $array ) );

		$this->assertEquals( $this->object->getId(), $array['order.coupon.id'] );
		$this->assertEquals( $this->object->getSiteId(), $array['order.coupon.siteid'] );
		$this->assertEquals( $this->object->getParentId(), $array['order.coupon.parentid'] );
		$this->assertEquals( $this->object->getCode(), $array['order.coupon.code'] );
		$this->assertEquals( $this->object->getProductId(), $array['order.coupon.productid'] );
		$this->assertEquals( $this->object->getTimeModified(), $array['order.coupon.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $array['order.coupon.ctime'] );
		$this->assertEquals( $this->object->editor(), $array['order.coupon.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

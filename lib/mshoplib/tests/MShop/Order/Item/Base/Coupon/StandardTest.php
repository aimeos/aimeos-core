<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Order\Item\Base\Coupon;


/**
 * Test class for \Aimeos\MShop\Order\Item\Base\Coupon\Standard.
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
			'order.base.coupon.id' => 1,
			'order.base.coupon.siteid' => 99,
			'order.base.coupon.baseid' => 42,
			'order.base.coupon.code' => 'SomeCode',
			'order.base.coupon.ordprodid' => 566778,
			'order.base.coupon.mtime' => '2001-12-30 23:59:59',
			'order.base.coupon.ctime' => '2011-01-01 00:00:01',
			'order.base.coupon.editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Base\Coupon\Standard( $this->values );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Coupon\Iface::class, $return );
		$this->assertEquals( null, $this->object->getId() );
		$this->assertTrue( $this->object->isModified() );

		$return = $this->object->setId( 5 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Coupon\Iface::class, $return );
		$this->assertEquals( 5, $this->object->getId() );
		$this->assertFalse( $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}

	public function testGetBaseId()
	{
		$this->assertEquals( 42, $this->object->getBaseId() );
	}

	public function testSetBaseId()
	{
		$return = $this->object->setBaseId( 99 );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Coupon\Iface::class, $return );
		$this->assertEquals( 99, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'SomeCode', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$return = $this->object->setCode( 'testId' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Coupon\Iface::class, $return );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Coupon\Iface::class, $return );
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
		$this->assertEquals( 'unitTestUser', $this->object->getEditor() );
	}


	public function testGetResourceType()
	{
		$this->assertEquals( 'order/base/coupon', $this->object->getResourceType() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Coupon\Standard();

		$list = $entries = array(
			'order.base.coupon.id' => 1,
			'order.base.coupon.baseid' => 2,
			'order.base.coupon.productid' => 3,
			'order.base.coupon.code' => 'test',
		);

		$item = $item->fromArray( $entries, true );

		$this->assertEquals( [], $entries );
		$this->assertEquals( $list['order.base.coupon.id'], $item->getId() );
		$this->assertEquals( $list['order.base.coupon.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.base.coupon.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.base.coupon.code'], $item->getCode() );
	}


	public function testToArray()
	{
		$array = $this->object->toArray( true );

		$this->assertEquals( count( $this->values ), count( $array ) );

		$this->assertEquals( $this->object->getId(), $array['order.base.coupon.id'] );
		$this->assertEquals( $this->object->getSiteId(), $array['order.base.coupon.siteid'] );
		$this->assertEquals( $this->object->getBaseId(), $array['order.base.coupon.baseid'] );
		$this->assertEquals( $this->object->getCode(), $array['order.base.coupon.code'] );
		$this->assertEquals( $this->object->getProductId(), $array['order.base.coupon.productid'] );
		$this->assertEquals( $this->object->getTimeModified(), $array['order.base.coupon.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $array['order.base.coupon.ctime'] );
		$this->assertEquals( $this->object->getEditor(), $array['order.base.coupon.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

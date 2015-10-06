<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Coupon_Item_Code_Default.
 */
class MShop_Coupon_Item_Code_DefaultTest extends PHPUnit_Framework_TestCase
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
			'id' => '1',
			'siteid' => 123,
			'couponid' => '2',
			'code' => 'abcd',
			'count' => '100',
			'start' => null,
			'end' => null,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new MShop_Coupon_Item_Code_Default( $this->values );
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
		$this->assertEquals( 1, $this->object->getId() );
	}

	public function testSetId()
	{
		$this->object->setId( '1' );
		$this->assertEquals( 1, $this->object->getId() );

		$this->assertFalse( false, $this->object->isModified() );

		$this->object->setId( null );
		$this->assertEquals( null, $this->object->getId() );

		$this->assertEquals( true, $this->object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->object->getSiteId() );
	}

	public function testGetCouponId()
	{
		$this->assertEquals( 2, $this->object->getCouponId() );
	}

	public function testSetCouponId()
	{
		$this->object->setCouponId( '3' );
		$this->assertEquals( 3, $this->object->getCouponId() );

		$this->assertEquals( true, $this->object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'abcd', $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'dcba' );
		$this->assertEquals( 'dcba', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCount()
	{
		$this->assertEquals( 100, $this->object->getCount() );
	}

	public function testSetCount()
	{
		$this->object->setCount( 50 );
		$this->assertEquals( 50, $this->object->getCount() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetDateStart()
	{
		$this->assertNull( $this->object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$this->object->setDateStart( '2010-04-22 06:22:22' );
		$this->assertEquals( '2010-04-22 06:22:22', $this->object->getDateStart() );
	}

	public function testGetDateEnd()
	{
		$this->assertNull( $this->object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$this->object->setDateEnd( '2010-05-22 06:22:22' );
		$this->assertEquals( '2010-05-22 06:22:22', $this->object->getDateEnd() );
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
		$item = new MShop_Coupon_Item_Code_Default();

		$list = array(
			'coupon.code.id' => 1,
			'coupon.code.couponid' => 2,
			'coupon.code.code' => 'test',
			'coupon.code.count' => 100,
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['coupon.code.id'], $item->getId() );
		$this->assertEquals( $list['coupon.code.couponid'], $item->getCouponId() );
		$this->assertEquals( $list['coupon.code.code'], $item->getCode() );
		$this->assertEquals( $list['coupon.code.count'], $item->getCount() );
	}


	public function testToArray()
	{
		$arrayObject = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $arrayObject ) );

		$this->assertEquals( $this->object->getId(), $arrayObject['coupon.code.id'] );
		$this->assertEquals( $this->object->getCode(), $arrayObject['coupon.code.code'] );
		$this->assertEquals( $this->object->getCount(), $arrayObject['coupon.code.count'] );
		$this->assertEquals( $this->object->getCouponId(), $arrayObject['coupon.code.couponid'] );
		$this->assertEquals( $this->object->getTimeCreated(), $arrayObject['coupon.code.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $arrayObject['coupon.code.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $arrayObject['coupon.code.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

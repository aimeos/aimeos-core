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
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_values = array(
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

		$this->_object = new MShop_Coupon_Item_Code_Default( $this->_values );
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
		$this->assertEquals( 1, $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId( '1' );
		$this->assertEquals( 1, $this->_object->getId() );

		$this->assertFalse( false, $this->_object->isModified() );

		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );

		$this->assertEquals( true, $this->_object->isModified() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 123, $this->_object->getSiteId() );
	}

	public function testGetCouponId()
	{
		$this->assertEquals( 2, $this->_object->getCouponId() );
	}

	public function testSetCouponId()
	{
		$this->_object->setCouponId( '3' );
		$this->assertEquals( 3, $this->_object->getCouponId() );

		$this->assertEquals( true, $this->_object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( 'abcd', $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->_object->setCode( 'dcba' );
		$this->assertEquals( 'dcba', $this->_object->getCode() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetCount()
	{
		$this->assertEquals( 100, $this->_object->getCount() );
	}

	public function testSetCount()
	{
		$this->_object->setCount( 50 );
		$this->assertEquals( 50, $this->_object->getCount() );
		$this->assertTrue( $this->_object->isModified() );
	}

	public function testGetDateStart()
	{
		$this->assertNull( $this->_object->getDateStart() );
	}

	public function testSetDateStart()
	{
		$this->_object->setDateStart( '2010-04-22 06:22:22' );
		$this->assertEquals( '2010-04-22 06:22:22', $this->_object->getDateStart() );
	}

	public function testGetDateEnd()
	{
		$this->assertNull( $this->_object->getDateEnd() );
	}

	public function testSetDateEnd()
	{
		$this->_object->setDateEnd( '2010-05-22 06:22:22' );
		$this->assertEquals( '2010-05-22 06:22:22', $this->_object->getDateEnd() );
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
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['coupon.code.id'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['coupon.code.code'] );
		$this->assertEquals( $this->_object->getCount(), $arrayObject['coupon.code.count'] );
		$this->assertEquals( $this->_object->getCouponId(), $arrayObject['coupon.code.couponid'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['coupon.code.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['coupon.code.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['coupon.code.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}

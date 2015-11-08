<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Order\Item\Base\Coupon;


/**
 * Test class for \Aimeos\MShop\Order\Item\Base\Coupon\Standard.
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
			'id' => 1,
			'siteid' => 99,
			'baseid' => 42,
			'code' => 'SomeCode',
			'ordprodid' => 566778,
			'mtime' => '2001-12-30 23:59:59',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->object = new \Aimeos\MShop\Order\Item\Base\Coupon\Standard( $this->values );
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

		$this->object->setId( 5 );
		$this->assertEquals( 5, $this->object->getId() );

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
		$this->assertEquals( $this->values['baseid'], $this->object->getBaseId() );
	}

	public function testSetBaseId()
	{
		$this->object->setBaseId( 99 );
		$this->assertEquals( 99, $this->object->getBaseId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetCode()
	{
		$this->assertEquals( $this->values['code'], $this->object->getCode() );
	}

	public function testSetCode()
	{
		$this->object->setCode( 'testId' );
		$this->assertEquals( 'testId', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetProductId()
	{
		$this->assertEquals( $this->values['ordprodid'], $this->object->getProductId() );
	}

	public function testSetProductId()
	{
		$this->object->setProductId( 12345 );
		$this->assertEquals( 12345, $this->object->getProductId() );
		$this->assertTrue( $this->object->isModified() );
	}

	public function testGetTimeModified()
	{
		$this->assertEquals( $this->values['mtime'], $this->object->getTimeModified() );
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

		$list = array(
			'order.base.coupon.id' => 1,
			'order.base.coupon.baseid' => 2,
			'order.base.coupon.productid' => 3,
			'order.base.coupon.code' => 'test',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['order.base.coupon.id'], $item->getId() );
		$this->assertEquals( $list['order.base.coupon.baseid'], $item->getBaseId() );
		$this->assertEquals( $list['order.base.coupon.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.base.coupon.code'], $item->getCode() );
	}


	public function testToArray()
	{
		$array = $this->object->toArray();
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

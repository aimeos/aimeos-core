<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Order_Item_Base_Coupon_Default.
 */
class MShop_Order_Item_Base_Coupon_DefaultTest extends MW_Unittest_Testcase
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
			'id' => 1,
			'siteid' => 99,
			'baseid' => 42,
			'code' => 'SomeCode',
			'ordprodid' => 566778,
			'mtime' => '2001-12-30 23:59:59',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Order_Item_Base_Coupon_Default( $this->_values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset($this->_object);
	}

	public function testGetId()
	{
		$this->assertEquals($this->_values['id'], $this->_object->getId());
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertEquals(null, $this->_object->getId() );

		$this->_object->setId(5);
		$this->assertEquals(5, $this->_object->getId() );

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
		$this->assertEquals($this->_values['baseid'], $this->_object->getBaseId());
	}

	public function testSetBaseId()
	{
		$this->_object->setBaseId(99);
		$this->assertEquals(99, $this->_object->getBaseId());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetCode()
	{
		$this->assertEquals($this->_values['code'], $this->_object->getCode());
	}

	public function testSetCode()
	{
		$this->_object->setCode('testId');
		$this->assertEquals('testId', $this->_object->getCode());
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetProductId()
	{
		$this->assertEquals($this->_values['ordprodid'], $this->_object->getProductId());
	}

	public function testSetProductId()
	{
		$this->_object->setProductId(12345);
		$this->assertEquals(12345, $this->_object->getProductId() );
		$this->assertTrue($this->_object->isModified());
	}

	public function testGetTimeModified()
	{
		$this->assertEquals($this->_values['mtime'], $this->_object->getTimeModified());
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
		$array = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $array ) );

		$this->assertEquals($this->_object->getId(), $array['order.base.coupon.id']);
		$this->assertEquals($this->_object->getSiteId(), $array['order.base.coupon.siteid']);
		$this->assertEquals($this->_object->getBaseId(), $array['order.base.coupon.baseid']);
		$this->assertEquals($this->_object->getCode(), $array['order.base.coupon.code']);
		$this->assertEquals($this->_object->getProductId(), $array['order.base.coupon.productid']);
		$this->assertEquals($this->_object->getTimeModified(), $array['order.base.coupon.mtime']);
		$this->assertEquals( $this->_object->getTimeCreated(), $array['order.base.coupon.ctime']);
		$this->assertEquals( $this->_object->getEditor(), $array['order.base.coupon.editor']);
	}

	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}
}

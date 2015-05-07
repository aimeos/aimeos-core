<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Product_Item_Stock_Warehouse_Default.
 */
class MShop_Product_Item_Stock_Warehouse_DefaultTest extends MW_Unittest_Testcase
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
			'id' => 44,
			'siteid'=>99,
			'code' => 'unit_label',
			'label' => 'label',
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Product_Item_Stock_Warehouse_Default( $this->_values );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}

	public function testGetId()
	{
		$this->assertEquals( $this->_values['id'], $this->_object->getId() );
	}

	public function testSetId()
	{
		$this->_object->setId(null);
		$this->assertTrue( $this->_object->isModified() );

		$this->assertNull( $this->_object->getId() );
	}

	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}

	public function testGetCode()
	{
		$this->assertEquals( $this->_values['code'], $this->_object->getCode() );
	}

	public function testSetCode()
	{
		$this->_object->setCode( 'test_label' );
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 'test_label', $this->_object->getCode() );
	}

	public function testGetLabel()
	{
		$this->assertEquals( $this->_values['label'], $this->_object->getLabel() );
	}

	public function testSetLabel()
	{
		$this->_object->setLabel( 'label123' );
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 'label123', $this->_object->getLabel() );
	}

	public function testGetStatus()
	{
		$this->assertEquals( $this->_values['status'], $this->_object->getStatus() );
	}

	public function testSetStatus()
	{
		$this->_object->setStatus( 0 );
		$this->assertTrue( $this->_object->isModified() );

		$this->assertEquals( 0, $this->_object->getStatus() );
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
		$item = new MShop_Product_Item_Stock_Warehouse_Default();

		$list = array(
			'product.stock.warehouse.id' => 1,
			'product.stock.warehouse.code' => 'test',
			'product.stock.warehouse.label' => 'test item',
			'product.stock.warehouse.status' => 0,
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array(), $unknown);

		$this->assertEquals($list['product.stock.warehouse.id'], $item->getId());
		$this->assertEquals($list['product.stock.warehouse.code'], $item->getCode());
		$this->assertEquals($list['product.stock.warehouse.label'], $item->getLabel());
		$this->assertEquals($list['product.stock.warehouse.status'], $item->getStatus());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['product.stock.warehouse.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['product.stock.warehouse.siteid'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['product.stock.warehouse.code'] );
		$this->assertEquals( $this->_object->getLabel(), $arrayObject['product.stock.warehouse.label'] );
		$this->assertEquals( $this->_object->getStatus(), $arrayObject['product.stock.warehouse.status'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['product.stock.warehouse.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['product.stock.warehouse.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['product.stock.warehouse.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}

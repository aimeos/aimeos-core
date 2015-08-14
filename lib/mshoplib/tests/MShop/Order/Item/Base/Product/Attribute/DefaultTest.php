<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Order_Item_Base_Product_Attribute_Default.
 */
class MShop_Order_Item_Base_Product_Attribute_DefaultTest extends PHPUnit_Framework_TestCase
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
			'id' => 4,
			'siteid' => 99,
			'attrid' => 22,
			'ordprodid' => 11,
			'type' => 'UnitType',
			'code' => 'size',
			'value' => '30',
			'name' => 'small',
			'mtime' => '2011-01-06 13:20:34',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Order_Item_Base_Product_Attribute_Default( $this->_values );
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
		$this->assertEquals( $this->_values['id'], $this->_object->getId() );
	}


	public function testSetId()
	{
		$this->_object->setId( null );
		$this->assertEquals( null, $this->_object->getId() );
		$this->assertTrue( $this->_object->isModified() );

		$this->_object->setId( 8 );
		$this->assertEquals( 8, $this->_object->getId() );
		$this->assertFalse( $this->_object->isModified() );

		$this->setExpectedException( 'MShop_Exception' );
		$this->_object->setId( 3 );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->_object->getSiteId() );
	}


	public function testGetAttributeId()
	{
		$this->assertEquals( $this->_values['attrid'], $this->_object->getAttributeId() );
	}


	public function testSetAttributeId()
	{
		$this->_object->setAttributeId( 44 );
		$this->assertEquals( 44, $this->_object->getAttributeId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetProductId()
	{
		$this->assertEquals( $this->_values['ordprodid'], $this->_object->getProductId() );
	}


	public function testSetProductId()
	{
		$this->_object->setProductId( 33 );
		$this->assertEquals( 33, $this->_object->getProductId() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( $this->_values['type'], $this->_object->getType() );
	}


	public function testSetType()
	{
		$this->_object->setType( 'testType' );
		$this->assertEquals( 'testType', $this->_object->getType() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( $this->_values['code'], $this->_object->getCode() );
	}


	public function testSetCode()
	{
		$this->_object->setCode( 'weight' );
		$this->assertEquals( 'weight', $this->_object->getCode() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( $this->_values['value'], $this->_object->getValue() );
	}


	public function testSetValue()
	{
		$this->_object->setValue( 36 );
		$this->assertEquals( 36, $this->_object->getValue() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( $this->_values['name'], $this->_object->getName() );
	}


	public function testSetName()
	{
		$this->_object->setName( 'medium' );
		$this->assertEquals( 'medium', $this->_object->getName() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetTimeModified()
	{
		$regexp = '/^[0-9]{4}-[0-1][0-9]-[0-3][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/';
		$this->assertRegExp( $regexp, $this->_object->getTimeModified() );
	}


	public function testGetTimeCreated()
	{
		$this->assertEquals( '2011-01-01 00:00:01', $this->_object->getTimeCreated() );
	}


	public function testGetEditor()
	{
		$this->assertEquals( 'unitTestUser', $this->_object->getEditor() );
	}


	public function testCopyFrom()
	{
		$attrManager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$items = $attrManager->searchItems( $attrManager->createSearch() );
		if( ( $item = reset( $items ) ) === false ) {
			throw new Exception( 'No attribute item found' );
		}

		$this->_object->copyFrom( $item );

		$this->assertEquals( $item->getId(), $this->_object->getAttributeId() );
		$this->assertEquals( $item->getLabel(), $this->_object->getName() );
		$this->assertEquals( $item->getType(), $this->_object->getCode() );
		$this->assertEquals( $item->getCode(), $this->_object->getValue() );
	}


	public function testFromArray()
	{
		$item = new MShop_Order_Item_Base_Product_Attribute_Default();

		$list = array(
			'order.base.product.attribute.id' => 1,
			'order.base.product.attribute.attrid' => 2,
			'order.base.product.attribute.productid' => 3,
			'order.base.product.attribute.type' => 'variant',
			'order.base.product.attribute.code' => 'test',
			'order.base.product.attribute.value' => 'value',
			'order.base.product.attribute.name' => 'test item',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['order.base.product.attribute.id'], $item->getId() );
		$this->assertEquals( $list['order.base.product.attribute.attrid'], $item->getAttributeId() );
		$this->assertEquals( $list['order.base.product.attribute.productid'], $item->getProductId() );
		$this->assertEquals( $list['order.base.product.attribute.type'], $item->getType() );
		$this->assertEquals( $list['order.base.product.attribute.code'], $item->getCode() );
		$this->assertEquals( $list['order.base.product.attribute.value'], $item->getValue() );
		$this->assertEquals( $list['order.base.product.attribute.name'], $item->getName() );
	}


	public function testToArray()
	{
		$list = $this->_object->toArray();

		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( $this->_object->getId(), $list['order.base.product.attribute.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['order.base.product.attribute.siteid'] );
		$this->assertEquals( $this->_object->getAttributeId(), $list['order.base.product.attribute.attrid'] );
		$this->assertEquals( $this->_object->getProductId(), $list['order.base.product.attribute.productid'] );
		$this->assertEquals( $this->_object->getType(), $list['order.base.product.attribute.type'] );
		$this->assertEquals( $this->_object->getCode(), $list['order.base.product.attribute.code'] );
		$this->assertEquals( $this->_object->getValue(), $list['order.base.product.attribute.value'] );
		$this->assertEquals( $this->_object->getName(), $list['order.base.product.attribute.name'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.base.product.attribute.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['order.base.product.attribute.ctime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['order.base.product.attribute.editor'] );
	}


	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}

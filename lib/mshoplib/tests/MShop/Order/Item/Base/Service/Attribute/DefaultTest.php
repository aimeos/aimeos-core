<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Order_Item_Base_Service_Attribute_Default.
 */
class MShop_Order_Item_Base_Service_Attribute_DefaultTest extends PHPUnit_Framework_TestCase
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
			'id' => 3,
			'siteid' => 99,
			'attrid' => 22,
			'ordservid' => 42,
			'type' => 'UnitType',
			'name' => 'UnitName',
			'code' => 'UnitCode',
			'value' => 'UnitValue',
			'mtime' => '2020-12-31 23:59:59',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Order_Item_Base_Service_Attribute_Default( $this->_values );
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

		$this->_object->setId( 99 );
		$this->assertEquals( 99, $this->_object->getId() );

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


	public function testGetServiceId()
	{
		$this->assertEquals( $this->_values['ordservid'], $this->_object->getServiceId() );
		$this->assertFalse( $this->_object->isModified() );
	}


	public function testSetServiceId()
	{
		$this->_object->setServiceId( 98 );
		$this->assertEquals( 98, $this->_object->getServiceId() );
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
		$this->_object->setCode( 'testCode' );
		$this->assertEquals( 'testCode', $this->_object->getCode() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( $this->_values['value'], $this->_object->getValue() );
	}


	public function testSetValue()
	{
		$this->_object->setValue( 'custom' );
		$this->assertEquals( 'custom', $this->_object->getValue() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( $this->_values['name'], $this->_object->getName() );
	}


	public function testSetName()
	{
		$this->_object->setName( 'testName' );
		$this->assertEquals( 'testName', $this->_object->getName() );
		$this->assertTrue( $this->_object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2020-12-31 23:59:59', $this->_object->getTimeModified() );
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
		$item = new MShop_Order_Item_Base_Service_Attribute_Default();

		$list = array(
			'order.base.service.attribute.id' => 1,
			'order.base.service.attribute.attrid' => 2,
			'order.base.service.attribute.serviceid' => 3,
			'order.base.service.attribute.type' => 'delivery',
			'order.base.service.attribute.code' => 'test',
			'order.base.service.attribute.value' => 'value',
			'order.base.service.attribute.name' => 'test item',
		);

		$unknown = $item->fromArray( $list );

		$this->assertEquals( array(), $unknown );

		$this->assertEquals( $list['order.base.service.attribute.id'], $item->getId() );
		$this->assertEquals( $list['order.base.service.attribute.attrid'], $item->getAttributeId() );
		$this->assertEquals( $list['order.base.service.attribute.serviceid'], $item->getServiceId() );
		$this->assertEquals( $list['order.base.service.attribute.type'], $item->getType() );
		$this->assertEquals( $list['order.base.service.attribute.code'], $item->getCode() );
		$this->assertEquals( $list['order.base.service.attribute.value'], $item->getValue() );
		$this->assertEquals( $list['order.base.service.attribute.name'], $item->getName() );
	}


	public function testToArray()
	{
		$list = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $list ) );

		$this->assertEquals( $this->_object->getId(), $list['order.base.service.attribute.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $list['order.base.service.attribute.siteid'] );
		$this->assertEquals( $this->_object->getAttributeId(), $list['order.base.service.attribute.attrid'] );
		$this->assertEquals( $this->_object->getServiceId(), $list['order.base.service.attribute.serviceid'] );
		$this->assertEquals( $this->_object->getType(), $list['order.base.service.attribute.type'] );
		$this->assertEquals( $this->_object->getCode(), $list['order.base.service.attribute.code'] );
		$this->assertEquals( $this->_object->getValue(), $list['order.base.service.attribute.value'] );
		$this->assertEquals( $this->_object->getName(), $list['order.base.service.attribute.name'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $list['order.base.service.attribute.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $list['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $list['order.base.service.attribute.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}

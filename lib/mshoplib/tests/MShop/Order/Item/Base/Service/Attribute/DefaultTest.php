<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Test class for MShop_Order_Item_Base_Service_Attribute_Default.
 */
class MShop_Order_Item_Base_Service_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;
	protected $_values;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Order_Item_Base_Service_Attribute_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


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
			'siteid'=>99,
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
		$this->assertEquals( $this->_values['type'], $this->_object->getType());
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
		$this->assertEquals($this->_values['value'], $this->_object->getValue());
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

		$this->assertEquals( $item->getLabel(), $this->_object->getName() );
		$this->assertEquals( $item->getType(), $this->_object->getCode() );
		$this->assertEquals( $item->getCode(), $this->_object->getValue() );
	}

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals( count( $this->_values ), count( $arrayObject ) );

		$this->assertEquals( $this->_object->getId(), $arrayObject['order.base.service.attribute.id'] );
		$this->assertEquals( $this->_object->getSiteId(), $arrayObject['order.base.service.attribute.siteid'] );
		$this->assertEquals( $this->_object->getServiceId(), $arrayObject['order.base.service.attribute.ordservid'] );
		$this->assertEquals( $this->_object->getType(), $arrayObject['order.base.service.attribute.type'] );
		$this->assertEquals( $this->_object->getCode(), $arrayObject['order.base.service.attribute.code']) ;
		$this->assertEquals( $this->_object->getValue(), $arrayObject['order.base.service.attribute.value'] );
		$this->assertEquals( $this->_object->getName(), $arrayObject['order.base.service.attribute.name'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->_object->getTimeCreated(), $arrayObject['order.base.service.attribute.ctime'] );
		$this->assertEquals( $this->_object->getTimeModified(), $arrayObject['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->_object->getEditor(), $arrayObject['order.base.service.attribute.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->_object->isModified() );
	}
}

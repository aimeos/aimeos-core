<?php

/**
 * @copyright Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MShop\Order\Item\Base\Service\Attribute;


/**
 * Test class for \Aimeos\MShop\Order\Item\Base\Service\Attribute\Standard.
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

		$this->object = new \Aimeos\MShop\Order\Item\Base\Service\Attribute\Standard( $this->values );
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
		$this->assertTrue( $this->object->isModified() );

		$this->object->setId( 99 );
		$this->assertEquals( 99, $this->object->getId() );

		$this->setExpectedException( '\\Aimeos\\MShop\\Exception' );
		$this->object->setId( 3 );
	}


	public function testGetSiteId()
	{
		$this->assertEquals( 99, $this->object->getSiteId() );
	}


	public function testGetAttributeId()
	{
		$this->assertEquals( $this->values['attrid'], $this->object->getAttributeId() );
	}


	public function testSetAttributeId()
	{
		$this->object->setAttributeId( 44 );
		$this->assertEquals( 44, $this->object->getAttributeId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetServiceId()
	{
		$this->assertEquals( $this->values['ordservid'], $this->object->getServiceId() );
		$this->assertFalse( $this->object->isModified() );
	}


	public function testSetServiceId()
	{
		$this->object->setServiceId( 98 );
		$this->assertEquals( 98, $this->object->getServiceId() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetType()
	{
		$this->assertEquals( $this->values['type'], $this->object->getType() );
	}


	public function testSetType()
	{
		$this->object->setType( 'testType' );
		$this->assertEquals( 'testType', $this->object->getType() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetCode()
	{
		$this->assertEquals( $this->values['code'], $this->object->getCode() );
	}


	public function testSetCode()
	{
		$this->object->setCode( 'testCode' );
		$this->assertEquals( 'testCode', $this->object->getCode() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetValue()
	{
		$this->assertEquals( $this->values['value'], $this->object->getValue() );
	}


	public function testSetValue()
	{
		$this->object->setValue( 'custom' );
		$this->assertEquals( 'custom', $this->object->getValue() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetName()
	{
		$this->assertEquals( $this->values['name'], $this->object->getName() );
	}


	public function testSetName()
	{
		$this->object->setName( 'testName' );
		$this->assertEquals( 'testName', $this->object->getName() );
		$this->assertTrue( $this->object->isModified() );
	}


	public function testGetTimeModified()
	{
		$this->assertEquals( '2020-12-31 23:59:59', $this->object->getTimeModified() );
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
		$this->assertEquals( 'order/base/service/attribute', $this->object->getResourceType() );
	}


	public function testCopyFrom()
	{
		$attrManager = \Aimeos\MShop\Attribute\Manager\Factory::createManager( \TestHelper::getContext() );

		$items = $attrManager->searchItems( $attrManager->createSearch() );
		if( ( $item = reset( $items ) ) === false ) {
			throw new \Exception( 'No attribute item found' );
		}

		$this->object->copyFrom( $item );

		$this->assertEquals( $item->getId(), $this->object->getAttributeId() );
		$this->assertEquals( $item->getLabel(), $this->object->getName() );
		$this->assertEquals( $item->getType(), $this->object->getCode() );
		$this->assertEquals( $item->getCode(), $this->object->getValue() );
	}


	public function testFromArray()
	{
		$item = new \Aimeos\MShop\Order\Item\Base\Service\Attribute\Standard();

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
		$list = $this->object->toArray();
		$this->assertEquals( count( $this->values ), count( $list ) );

		$this->assertEquals( $this->object->getId(), $list['order.base.service.attribute.id'] );
		$this->assertEquals( $this->object->getSiteId(), $list['order.base.service.attribute.siteid'] );
		$this->assertEquals( $this->object->getAttributeId(), $list['order.base.service.attribute.attrid'] );
		$this->assertEquals( $this->object->getServiceId(), $list['order.base.service.attribute.serviceid'] );
		$this->assertEquals( $this->object->getType(), $list['order.base.service.attribute.type'] );
		$this->assertEquals( $this->object->getCode(), $list['order.base.service.attribute.code'] );
		$this->assertEquals( $this->object->getValue(), $list['order.base.service.attribute.value'] );
		$this->assertEquals( $this->object->getName(), $list['order.base.service.attribute.name'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->object->getTimeCreated(), $list['order.base.service.attribute.ctime'] );
		$this->assertEquals( $this->object->getTimeModified(), $list['order.base.service.attribute.mtime'] );
		$this->assertEquals( $this->object->getEditor(), $list['order.base.service.attribute.editor'] );
	}

	public function testIsModified()
	{
		$this->assertFalse( $this->object->isModified() );
	}
}

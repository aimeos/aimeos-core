<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

/**
 * Test class for MShop_Attribute_Item_Example.
 */
class MShop_Attribute_Item_DefaultTest extends MW_Unittest_Testcase
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
			'id' => 999,
			'domain' => 'text',
			'code' => 'X12345',
			'status' => 1,
			'typeid' => 3,
			'type' => 'unittest',
			'pos' => 0,
			'label' => 'size',
			'siteid' => 99,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);

		$this->_object = new MShop_Attribute_Item_Default($this->_values);
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
		$this->assertEquals(999, $this->_object->getId());
	}


	public function testSetId()
	{
		$this->_object->setId(999);
		$this->assertEquals(999, $this->_object->getId());
		$this->assertFalse($this->_object->isModified());

		$this->_object->setId(null);

		$this->assertEquals(null, $this->_object->getId());
		$this->assertEquals(true, $this->_object->isModified());
	}


	public function testGetType()
	{
		$this->assertEquals('unittest', $this->_object->getType());
	}


	public function testGetTypeId()
	{
		$this->assertEquals(3, $this->_object->getTypeId());
	}


	public function testSetTypeId()
	{
		$this->_object->setTypeId(5);
		$this->assertEquals(5, $this->_object->getTypeId());
		$this->assertEquals(true, $this->_object->isModified());
	}


	public function testGetDomain()
	{
		$this->assertEquals('text', $this->_object->getDomain());
	}


	public function testSetDomain()
	{
		$this->_object->setDomain('TestDom');
		$this->assertEquals('TestDom', $this->_object->getDomain());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetCode()
	{
		$this->assertEquals('X12345', $this->_object->getCode());
	}


	public function testSetCode()
	{
		$this->_object->setCode('flobee');
		$this->assertEquals('flobee', $this->_object->getCode());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetPosition()
	{
		$this->assertEquals(0, $this->_object->getPosition());
	}


	public function testSetPosition()
	{
		$this->_object->setPosition(1);
		$this->assertEquals(1, $this->_object->getPosition());

		$this->assertTrue($this->_object->isModified());
	}


	public function testGetLabel()
	{
		$this->assertEquals('size', $this->_object->getLabel());
	}


	public function testSetLabel()
	{
		$this->_object->setLabel('weight');
		$this->assertEquals('weight', $this->_object->getLabel());

		$this->assertTrue($this->_object->isModified());
	}


	public function testGetStatus()
	{
		$this->assertEquals(1, $this->_object->getStatus());
	}


	public function testSetStatus()
	{
		$this->_object->setStatus(4);
		$this->assertEquals(4, $this->_object->getStatus());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetSiteId()
	{
		$this->assertEquals(99, $this->_object->getSiteId());
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
		$item = new MShop_Attribute_Item_Default();

		$list = array(
			'attribute.id' => 1,
			'attribute.siteid' => 2,
			'attribute.code' => 'test',
			'attribute.domain' => 'product',
			'attribute.status' => '0',
			'attribute.typeid' => 3,
			'attribute.type' => 'testtype',
			'attribute.label' => 'test attribute',
			'attribute.position' => 10,
			'attribute.ctime' => '2000-01-01 00:00:00',
			'attribute.mtime' => '2001-01-01 00:00:00',
			'attribute.editor' => 'test',
		);

		$unknown = $item->fromArray($list);

		$this->assertEquals(array('attribute.type' => 'testtype'), $unknown);

		$this->assertEquals($list['attribute.id'], $item->getId());
		$this->assertEquals($list['attribute.code'], $item->getCode());
		$this->assertEquals($list['attribute.domain'], $item->getDomain());
		$this->assertEquals($list['attribute.status'], $item->getStatus());
		$this->assertEquals($list['attribute.typeid'], $item->getTypeId());
		$this->assertEquals($list['attribute.label'], $item->getLabel());
		$this->assertEquals($list['attribute.position'], $item->getPosition());
		$this->assertNull($item->getSiteId());
		$this->assertNull($item->getType());
	}


	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals(count($this->_values), count($arrayObject));

		$this->assertEquals($this->_object->getId(), $arrayObject['attribute.id']);
		$this->assertEquals($this->_object->getCode(), $arrayObject['attribute.code']);
		$this->assertEquals($this->_object->getDomain(), $arrayObject['attribute.domain']);
		$this->assertEquals($this->_object->getStatus(), $arrayObject['attribute.status']);
		$this->assertEquals($this->_object->getTypeId(), $arrayObject['attribute.typeid']);
		$this->assertEquals($this->_object->getType(), $arrayObject['attribute.type']);
		$this->assertEquals($this->_object->getLabel(), $arrayObject['attribute.label']);
		$this->assertEquals($this->_object->getPosition(), $arrayObject['attribute.position']);
		$this->assertEquals($this->_object->getSiteId(), $arrayObject['attribute.siteid']);
		$this->assertEquals($this->_object->getTimeCreated(), $arrayObject['attribute.ctime']);
		$this->assertEquals($this->_object->getTimeModified(), $arrayObject['attribute.mtime']);
		$this->assertEquals($this->_object->getEditor(), $arrayObject['attribute.editor']);
	}


	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}

}

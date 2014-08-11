<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Locale_Item_Currency_Default.
 */
class MShop_Locale_Item_Currency_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	protected function setUp()
	{
		$this->values = array(
			'id' => 'EUR',
			'label' => 'Euro',
			'siteid' => 1,
			'status' => 1,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);
		$this->_object = new MShop_Locale_Item_Currency_Default($this->values);
	}


	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}


	public function testGetId()
	{
		$this->assertEquals('EUR', $this->_object->getId());
	}


	public function testSetIdBasic()
	{
		// test 1: set id and compare to be the same
		$this->_object->setId('XXX');
		$this->assertEquals('XXX', $this->_object->getId());
		// test modifier
		$this->assertFalse($this->_object->isModified());

		// test 2: set id to null, mdified should be true, id=null
		$var = null;
		$this->_object->setId($var);
		$this->assertEquals(null, $this->_object->getId());
		// test modifier
		$this->assertTrue($this->_object->isModified());
	}


	public function testSetIdLength()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setId('EU');
	}


	public function testSetIdNumeric()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setId(123);
	}


	public function testGetCode()
	{
		$this->assertEquals('EUR', $this->_object->getCode());
	}


	public function testSetCode()
	{
		$this->_object->setCode('USD');
		$this->assertEquals('USD', $this->_object->getCode());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetLabel()
	{
		$this->assertEquals('Euro', $this->_object->getLabel());
	}


	public function testSetLabel()
	{
		$this->_object->setLabel('OtherName');
		$this->assertEquals('OtherName', $this->_object->getLabel());
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetSiteId()
	{
		$this->assertEquals(1, $this->_object->getSiteId());
	}


	public function testGetStatus()
	{
		$this->assertEquals(1, $this->_object->getStatus());
	}


	public function testSetStatus()
	{
		$this->_object->setStatus(0);
		$this->assertEquals(0, $this->_object->getStatus());
		$this->assertTrue($this->_object->isModified());
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

	public function testToArray()
	{
		$arrayObject = $this->_object->toArray();
		$this->assertEquals((count($this->values) + 1), count($arrayObject));

		$this->assertEquals($this->_object->getId(), $arrayObject['locale.currency.id']);
		$this->assertEquals($this->_object->getCode(), $arrayObject['locale.currency.code']);
		$this->assertEquals($this->_object->getLabel(), $arrayObject['locale.currency.label']);
		$this->assertEquals($this->_object->getSiteId(), $arrayObject['locale.currency.siteid']);
		$this->assertEquals($this->_object->getStatus(), $arrayObject['locale.currency.status']);
		$this->assertEquals($this->_object->getTimeCreated(), $arrayObject['locale.currency.ctime'] );
		$this->assertEquals($this->_object->getTimeModified(), $arrayObject['locale.currency.mtime'] );
		$this->assertEquals($this->_object->getEditor(), $arrayObject['locale.currency.editor'] );
	}

}

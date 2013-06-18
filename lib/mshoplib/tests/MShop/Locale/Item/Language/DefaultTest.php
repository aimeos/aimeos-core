<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Test class for MShop_Locale_Item_Language_Default.
 */
class MShop_Locale_Item_Language_DefaultTest extends MW_Unittest_Testcase
{

	private $_object;


	protected function setUp()
	{
		$this->values = array(
			'id' => 'es',
			'label' => 'spanish',
			'siteid' => 1,
			'status' => 9,
			'mtime' => '2011-01-01 00:00:02',
			'ctime' => '2011-01-01 00:00:01',
			'editor' => 'unitTestUser'
		);
		$this->_object = new MShop_Locale_Item_Language_Default($this->values);
	}


	protected function tearDown()
	{
		$this->_object = null;
	}


	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite = new PHPUnit_Framework_TestSuite('MShop_Locale_Item_Language_DefaultTest');
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}


	public function testIsModified()
	{
		$this->assertFalse($this->_object->isModified());
	}


	public function testGetId()
	{
		$this->assertEquals('es', $this->_object->getId());
	}


	public function testSetId()
	{
		$this->_object->setId('de');
		$this->assertEquals('de', $this->_object->getId());
		$this->assertFalse($this->_object->isModified());

		$var = null;
		$this->_object->setId($var);
		$this->assertEquals(null, $this->_object->getId());
		$this->assertTrue($this->_object->isModified());
	}


	public function testSetIdLength()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setId('espania');
	}


	public function testSetIdNumeric()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setId(123);
	}


	public function testGetCode()
	{
		$this->assertEquals($this->_object->getId(), $this->_object->getCode());
	}


	public function testSetCodeInvalid()
	{
		$this->setExpectedException('MShop_Locale_Exception');
		$this->_object->setCode('XXX');
	}


	public function testGetLabel()
	{
		$this->assertEquals('spanish', $this->_object->getLabel());
	}


	public function testSetLabel()
	{
		$this->_object->setLabel('OtherName');
		$this->assertEquals('OtherName', $this->_object->getLabel());
		// test modifier
		$this->assertTrue($this->_object->isModified());
	}


	public function testGetSiteId()
	{
		$this->assertEquals(1, $this->_object->getSiteId());
	}


	public function testGetStatus()
	{
		$this->assertEquals(9, $this->_object->getStatus());
	}


	public function testSetStatus()
	{
		$this->_object->setStatus(0);
		$this->assertEquals(0, $this->_object->getStatus());
		// test modifier
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

		$this->assertEquals($this->_object->getId(), $arrayObject['locale.language.id']);
		$this->assertEquals($this->_object->getCode(), $arrayObject['locale.language.code']);
		$this->assertEquals($this->_object->getLabel(), $arrayObject['locale.language.label']);
		$this->assertEquals($this->_object->getSiteId(), $arrayObject['locale.language.siteid']);
		$this->assertEquals($this->_object->getStatus(), $arrayObject['locale.language.status']);
		$this->assertEquals($this->_object->getTimeCreated(), $arrayObject['locale.language.ctime'] );
		$this->assertEquals($this->_object->getTimeModified(), $arrayObject['locale.language.mtime'] );
		$this->assertEquals($this->_object->getEditor(), $arrayObject['locale.language.editor'] );
	}

}

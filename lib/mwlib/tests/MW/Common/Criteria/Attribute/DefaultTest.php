<?php

/**
 * Test class for MW_Common_Criteria_Attribute_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Common_Criteria_Attribute_DefaultTest extends MW_Unittest_Testcase
{
	/**
	 * @var    MW_Common_Criteria_Attribute_Default
	 * @access protected
	 */
	private $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$values = array(
			'type' => 'attribute_type',
			'internaltype' => 'internaltype',
			'code' => 'attribute_code',
			'internalcode' => 'internalcode',
			'internaldeps' => array( 'test' ),
			'label' => 'labelname',
			'default' => 'default value',
			'public' => false,
			'required' => false,
		);

		$this->_object = new MW_Common_Criteria_Attribute_Default($values);
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testGetType()
	{
		$this->assertEquals('attribute_type', $this->_object->getType());
	}


	public function testGetInternalType()
	{
		$this->assertEquals('internaltype', $this->_object->getInternalType());
	}

	public function testGetCode()
	{
		$this->assertEquals('attribute_code', $this->_object->getCode());
	}

	public function testGetInternalCode()
	{
		$this->assertEquals('internalcode', $this->_object->getInternalCode());
	}

	public function testGetInternalDeps()
	{
		$this->assertEquals(array( 'test' ), $this->_object->getInternalDeps());
	}

	public function testGetLabel()
	{
		$this->assertEquals('labelname', $this->_object->getLabel());
	}

	public function testGetDefault()
	{
		$this->assertEquals('default value', $this->_object->getDefault());
	}

	public function testIsPublic()
	{
		$this->assertEquals(false, $this->_object->isPublic());
	}

	public function testIsRequired()
	{
		$this->assertEquals(false, $this->_object->isRequired());
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


/**
 * Test class for MShop_Common_Item_Helper_Form_Default
 */
class MShop_Common_Item_Helper_Form_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_values;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_values = array(
			'name' => new MW_Common_Criteria_Attribute_Default( array(
				'code' => 'name',
				'internalcode' => 'name',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Name',
				'default' => 'unittest',
			) ),
			'site' => new MW_Common_Criteria_Attribute_Default( array(
				'code' => 'site',
				'internalcode' => 'site',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Site',
				'default' => 'de',
			) ),
			'language' => new MW_Common_Criteria_Attribute_Default( array(
				'code' => 'language',
				'internalcode' => 'language',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Language',
				'default' => 'de',
			) ),
			'language' => new MW_Common_Criteria_Attribute_Default( array(
				'code' => 'domain',
				'internalcode' => 'domain',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Domain',
				'default' => 'testDomain',
			) ),
		);

		$this->_object = new MShop_Common_Item_Helper_Form_Default( 'http://www.example.com', 'post', $this->_values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->_object, $this->_values );
	}

	public function testGetExternal()
	{
		$this->assertEquals( true, $this->_object->getExternal() );
	}

	public function testSetExternal()
	{
		$this->_object->setExternal( false );
		$this->assertEquals( false, $this->_object->getExternal() );
	}

	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.example.com', $this->_object->getUrl() );
	}

	public function testSetUrl()
	{
		$this->_object->setUrl( 'http://www.example.de' );
		$this->assertEquals( 'http://www.example.de', $this->_object->getUrl() );
	}

	public function testGetMethod()
	{
		$this->assertEquals( 'post', $this->_object->getMethod() );
	}

	public function testSetMethod()
	{
		$this->_object->setMethod( 'get' );
		$this->assertEquals( 'get', $this->_object->getMethod() );
	}

	public function testGetValues()
	{
		$this->assertEquals( $this->_values, $this->_object->getValues() );
	}

	public function testGetValue()
	{
		$this->assertEquals( 'unittest', $this->_object->getValue( 'name' )->getDefault() );
	}

	public function testSetValue()
	{
		$item = new MW_Common_Criteria_Attribute_Default( array(
			'code' => 'name',
			'internalcode' => 'name',
			'type' => 'string',
			'internaltype' => 'string',
			'label' => 'Name',
			'default' => 'test',
		) );

		$this->_object->setValue( 'name', $item );
		$this->assertEquals( 'test', $this->_object->getValue( 'name' )->getDefault() );
	}
}

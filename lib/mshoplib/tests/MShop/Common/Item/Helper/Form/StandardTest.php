<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


/**
 * Test class for MShop_Common_Item_Helper_Form_Standard
 */
class MShop_Common_Item_Helper_Form_StandardTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $values;


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->values = array(
			'name' => new MW_Common_Criteria_Attribute_Standard( array(
				'code' => 'name',
				'internalcode' => 'name',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Name',
				'default' => 'unittest',
			) ),
			'site' => new MW_Common_Criteria_Attribute_Standard( array(
				'code' => 'site',
				'internalcode' => 'site',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Site',
				'default' => 'de',
			) ),
			'language' => new MW_Common_Criteria_Attribute_Standard( array(
				'code' => 'language',
				'internalcode' => 'language',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Language',
				'default' => 'de',
			) ),
			'language' => new MW_Common_Criteria_Attribute_Standard( array(
				'code' => 'domain',
				'internalcode' => 'domain',
				'type' => 'string',
				'internaltype' => 'string',
				'label' => 'Domain',
				'default' => 'testDomain',
			) ),
		);

		$this->object = new MShop_Common_Item_Helper_Form_Standard( 'http://www.example.com', 'post', $this->values );
	}


	/**
	 * Tears down the fixture. This method is called after a test is executed.
	 */
	protected function tearDown()
	{
		unset( $this->object, $this->values );
	}

	public function testGetExternal()
	{
		$this->assertEquals( true, $this->object->getExternal() );
	}

	public function testSetExternal()
	{
		$this->object->setExternal( false );
		$this->assertEquals( false, $this->object->getExternal() );
	}

	public function testGetUrl()
	{
		$this->assertEquals( 'http://www.example.com', $this->object->getUrl() );
	}

	public function testSetUrl()
	{
		$this->object->setUrl( 'http://www.example.de' );
		$this->assertEquals( 'http://www.example.de', $this->object->getUrl() );
	}

	public function testGetMethod()
	{
		$this->assertEquals( 'post', $this->object->getMethod() );
	}

	public function testSetMethod()
	{
		$this->object->setMethod( 'get' );
		$this->assertEquals( 'get', $this->object->getMethod() );
	}

	public function testGetValues()
	{
		$this->assertEquals( $this->values, $this->object->getValues() );
	}

	public function testGetValue()
	{
		$this->assertEquals( 'unittest', $this->object->getValue( 'name' )->getDefault() );
	}

	public function testSetValue()
	{
		$item = new MW_Common_Criteria_Attribute_Standard( array(
			'code' => 'name',
			'internalcode' => 'name',
			'type' => 'string',
			'internaltype' => 'string',
			'label' => 'Name',
			'default' => 'test',
		) );

		$this->object->setValue( 'name', $item );
		$this->assertEquals( 'test', $this->object->getValue( 'name' )->getDefault() );
	}
}

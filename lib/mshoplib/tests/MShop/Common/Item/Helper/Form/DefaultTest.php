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
	 * Runs the test methods of this class.
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite('MShop_Common_Item_Helper_Form_DefaultTest');
		PHPUnit_TextUI_TestRunner::run($suite);
	}


	/**
	 * Sets up the fixture. This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_values = array(
			'name' => 'unittest',
			'site' => 'de',
			'language' => 'de',
			'domain' => 'testDomain',
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
		$this->assertEquals( 'unittest', $this->_object->getValue( 'name' ) );
	}

	public function testSetValue()
	{
		$this->_object->setValue( 'name', 'test' );
		$this->assertEquals( 'test', $this->_object->getValue( 'name' ) );
	}
}

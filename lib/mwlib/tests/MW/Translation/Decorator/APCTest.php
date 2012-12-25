<?php

/**
 * Test class for MW_Translation_Decorator_APC.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Translation_Decorator_APCTest extends MW_Unittest_Testcase
{
	protected $_object;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_Translation_Decorator_APCTest');
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
		if( function_exists( 'apc_store' ) === false ) {
			$this->markTestSkipped( 'APC not installed' );
		}

		$trans = new MW_Translation_None( 'en_GB' );
		$config = new MW_Config_Zend( new Zend_Config( array() ) );
		$this->_object = new MW_Translation_Decorator_APC( $trans, $config );
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

	public function testDt()
	{
		$this->assertEquals( 'test', $this->_object->dt( 'domain', 'test' ) );
	}

	public function testDn()
	{
		$this->assertEquals( 'tests', $this->_object->dn( 'domain', 'test', 'tests', 2 ) );
	}

	public function testGetLocale()
	{
		$this->assertEquals( 'en_GB', $this->_object->getLocale() );
	}
}

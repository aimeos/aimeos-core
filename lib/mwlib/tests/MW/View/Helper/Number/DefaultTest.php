<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id$
 */


/**
 * Test class for MW_View_Helper_Number.
 */
class MW_View_Helper_Number_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_View_Helper_Number_Default');
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
		$view = new MW_View_Default();
		$this->_object = new MW_View_Helper_Number_Default( $view, '.', ' ' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( '1.00', $this->_object->transform( 1 ) );
		$this->assertEquals( '1.00', $this->_object->transform( 1.0 ) );
		$this->assertEquals( '1 000.00', $this->_object->transform( 1000.0 ) );
	}


	public function testTransformNoDecimals()
	{
		$this->assertEquals( '1', $this->_object->transform( 1, 0 ) );
		$this->assertEquals( '1', $this->_object->transform( 1.0, 0 ) );
		$this->assertEquals( '1 000', $this->_object->transform( 1000.0, 0 ) );
	}

}

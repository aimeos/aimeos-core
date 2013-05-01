<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id$
 */


/**
 * Test class for MW_View_Helper_Config.
 */
class MW_View_Helper_Config_DefaultTest extends MW_Unittest_Testcase
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
		$suite  = new PHPUnit_Framework_TestSuite('MW_View_Helper_Config_Default');
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

		$config = array(
			'page' => 'test',
			'sub' => array(
				'subpage' => 'test2',
			),
		);

		$conf = new MW_Config_Array( $config );
		$this->_object = new MW_View_Helper_Config_Default( $view, $conf );
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
		$this->assertEquals( 'test', $this->_object->transform( 'page', 'none' ) );
		$this->assertEquals( 'none', $this->_object->transform( 'missing', 'none' ) );
	}


	public function testTransformPath()
	{
		$this->assertEquals( 'test2', $this->_object->transform( 'sub/subpage', 'none' ) );
		$this->assertEquals( array( 'subpage' => 'test2' ), $this->_object->transform( 'sub' ) );
	}


	public function testTransformNoDefault()
	{
		$this->assertEquals( 'test', $this->_object->transform( 'page' ) );
		$this->assertEquals( null, $this->_object->transform( 'missing' ) );
	}

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @version $Id$
 */


/**
 * Test class for MW_View_Helper_Translate.
 */
class MW_View_Helper_Translate_DefaultTest extends MW_Unittest_Testcase
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
		$suite  = new PHPUnit_Framework_TestSuite('MW_View_Helper_Translate_Default');
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
		$ds = DIRECTORY_SEPARATOR;

		$translationSources = array(
			'test' => dirname(__FILE__) . $ds . 'testfiles' . $ds . 'translate',
		);

		$view = new MW_View_Default();
		$translate = new MW_Translation_None( 'en_GB' );
		$this->_object = new MW_View_Helper_Translate_Default( $view, $translate );
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
		$this->assertEquals( 'File', $this->_object->transform( 'test', 'File', 'Files', 1 ) );
		$this->assertEquals( 'Files', $this->_object->transform( 'test', 'File', 'Files', 2 ) );
	}

}

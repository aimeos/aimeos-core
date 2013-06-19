<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */


/**
 * Test class for MW_View_Default.
 */
class MW_View_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_translate;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		$suite  = new PHPUnit_Framework_TestSuite('MW_View_Helper_Default');
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
		$this->_object = new MW_View_Default();
		$this->_translate = new MW_View_Helper_Translate_Default( $this->_object, new MW_Translation_None( 'en_GB' ) );
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


	public function testMagicMethods()
	{
		$this->assertEquals( false, isset( $this->_object->test ) );

		$this->_object->test = 10;
		$this->assertEquals( 10, $this->_object->test );
		$this->assertEquals( true, isset( $this->_object->test ) );

		unset( $this->_object->test );
		$this->assertEquals( false, isset( $this->_object->test ) );

		$this->setExpectedException( 'MW_View_Exception' );
		$this->_object->test;
	}


	public function testGet()
	{
		$this->assertEquals( null, $this->_object->get( 'test' ) );
		$this->assertEquals( 1, $this->_object->get( 'test', 1 ) );

		$this->_object->test = 10;
		$this->assertEquals( 10, $this->_object->get( 'test' ) );
	}


	public function testCallException()
	{
		$this->setExpectedException( 'MW_View_Exception' );
		$this->assertEquals( 'File', $this->_object->translate( 'test', 'File', 'Files', 1 ) );
	}


	public function testCallAddHelper()
	{
		$this->_object->addHelper( 'translate', $this->_translate );

		$this->assertEquals( 'File', $this->_object->translate( 'test', 'File', 'Files', 1 ) );
	}


	public function testAssignRender()
	{
		$this->_object->addHelper( 'translate', $this->_translate );
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles'. DIRECTORY_SEPARATOR . 'template';


		$this->_object->assign( array( 'quantity' => 1 ) );
		$output = $this->_object->render( $filename );

		$expected = "Number of files:\n1 File";
		$this->assertEquals( $expected, $output );


		$this->_object->assign( array( 'quantity' => 0 ) );
		$output = $this->_object->render( $filename );

		$expected = "Number of files:\n0 Files";
		$this->assertEquals( $expected, $output );
	}
}

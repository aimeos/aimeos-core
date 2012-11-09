<?php

/**
 * Test class for MW_Logger_Zend.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Logger_ErrorlogTest extends MW_Unittest_Testcase
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
		$suite  = new PHPUnit_Framework_TestSuite('MW_Logger_ZendTest');
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
		$this->_object = new MW_Logger_Errorlog( MW_Logger_Abstract::DEBUG );
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


	public function testLog()
	{
		ini_set("error_log", "error.log");

		$this->_object->log( 'error test' );
		$this->_object->log( 'warning test', MW_Logger_Abstract::WARN );
		$this->_object->log( 'notice test', MW_Logger_Abstract::NOTICE );
		$this->_object->log( 'info test', MW_Logger_Abstract::INFO );
		$this->_object->log( 'debug test', MW_Logger_Abstract::DEBUG );
		$this->_object->log(array('scalar', 'test'));

		if( ( $content = file( 'error.log' ) ) === false ) {
			throw new Exception( 'Unable to open file "error.log"' );
		}

		unlink( "error.log" );
		ini_restore( "error_log" );

		foreach( $content as $line ) {
			$this->assertRegExp( '/\[[^\]]+\] <message> \[[^\]]+\] .+test/', $line, $line );
		}
	}


	public function testLogLevel()
	{
		$this->setExpectedException( 'MW_Logger_Exception' );
		$this->_object->log( 'wrong loglevel test', -1 );
	}
}

<?php

/**
 * Test class for MW_Logger_Zend.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Logger_ZendTest extends PHPUnit_Extensions_OutputTestCase
{
	/**
	 * @var    MW_Logger_Zend
	 * @access protected
	 */
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
		if( class_exists( 'Zend_Log' ) === false ) {
			$this->markTestSkipped( 'Class Zend_Log not found' );
		}

		$writer = new Zend_Log_Writer_Stream('php://output');

		$formatter = new Zend_Log_Formatter_Simple('log: %message%' . PHP_EOL);
		$writer->setFormatter($formatter);

		$logger = new Zend_Log($writer);

		$filter = new Zend_Log_Filter_Priority(Zend_Log::INFO);
		$logger->addFilter($filter);

		$this->_object = new MW_Logger_Zend( $logger );
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
		$this->expectOutputString("log: <message> error\n");
		$this->_object->log( 'error' );
	}

	public function testNonScalarLog()
	{
		$this->expectOutputString('log: <message> ["error","error2",2]' . PHP_EOL);
		$this->_object->log( array ('error', 'error2', 2) );
	}

	public function testBadPriority()
	{
		$this->setExpectedException('MW_Logger_Exception');
		$this->_object->log( 'error', -1 );
	}

	public function testLogDebug()
	{
		$this->expectOutputString('');
		$this->_object->log( 'debug', MW_Logger_Abstract::DEBUG );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_Logger_ErrorlogTest extends PHPUnit_Framework_TestCase
{
	private $_object;


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
		@unlink( "error.log" );
	}


	public function testLog()
	{
		if( defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'Hiphop VM does not support ini settings yet' );
		}

		ini_set( "error_log", "error.log" );

		$this->_object->log( 'error test' );
		$this->_object->log( 'warning test', MW_Logger_Abstract::WARN );
		$this->_object->log( 'notice test', MW_Logger_Abstract::NOTICE );
		$this->_object->log( 'info test', MW_Logger_Abstract::INFO );
		$this->_object->log( 'debug test', MW_Logger_Abstract::DEBUG );
		$this->_object->log( array( 'scalar', 'test' ) );

		if( ( $content = file( 'error.log' ) ) === false ) {
			throw new Exception( 'Unable to open file "error.log"' );
		}

		ini_restore( "error_log" );

		foreach( $content as $line ) {
			$this->assertRegExp( '/\[[^\]]+\] <message> \[[^\]]+\] .+test/', $line, $line );
		}
	}


	public function testLogFacility()
	{
		if( defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'Hiphop VM does not support ini settings yet' );
		}

		ini_set( "error_log", "error.log" );

		$this->_object = new MW_Logger_Errorlog( MW_Logger_Abstract::DEBUG, array('test') );
		$this->_object->log( 'info test', MW_Logger_Abstract::INFO, 'info' );

		ini_restore( "error_log" );

		if( file_exists( 'error.log' ) ) {
			throw new Exception( 'File "error.log" should not be created' );
		}
	}


	public function testLogLevel()
	{
		$this->setExpectedException( 'MW_Logger_Exception' );
		$this->_object->log( 'wrong loglevel test', -1 );
	}
}

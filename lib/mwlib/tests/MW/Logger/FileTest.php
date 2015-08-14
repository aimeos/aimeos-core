<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_Logger_FileTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_filename;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_filename = 'loggertest.log';
		$this->_object = new MW_Logger_File( $this->_filename );
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unlink( 'loggertest.log' );
	}

	public function testLog()
	{
		$this->_object->log( 'error' );

		if( !file_exists( $this->_filename ) ) {
			throw new Exception( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->_filename ) );

		if( empty( $msg ) ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( MW_Logger_Abstract::ERR, $msg[3] );
		$this->assertEquals( 'error', $msg[4] );


		$this->setExpectedException('MW_Logger_Exception');
		$this->_object->log( 'wrong log level', -1);
	}

	public function testScalarLog()
	{
		$this->_object->log( array ( 'scalar', 'errortest' ) );

		if( !file_exists( $this->_filename ) ) {
			throw new Exception( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->_filename ) );

		if( empty( $msg ) ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( MW_Logger_Abstract::ERR, $msg[3] );
		$this->assertEquals( '["scalar","errortest"]', $msg[4] );
	}

	public function testLogCrit()
	{
		$this->_object->log( 'critical', MW_Logger_Abstract::CRIT );

		if( !file_exists( $this->_filename ) ) {
			throw new Exception( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->_filename ) );

		if( empty( $msg ) ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( MW_Logger_Abstract::CRIT, $msg[3] );
		$this->assertEquals( 'critical', $msg[4] );
	}

	public function testLogWarn()
	{
		$this->_object->log( 'debug', MW_Logger_Abstract::WARN );

		if( !file_exists( $this->_filename ) ) {
			throw new Exception( 'No test file found' );
		}

		$msg = file_get_contents( $this->_filename );

		if( $msg !== '' ) {
			throw new Exception( 'Log record found but none expected' );
		}
	}

	public function testFacility()
	{
		$this->_object->log( 'user auth', MW_Logger_Abstract::ERR, 'auth' );

		if( !file_exists( $this->_filename ) ) {
			throw new Exception( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->_filename ) );

		if( empty( $msg ) ) {
			throw new Exception( 'No log record found' );
		}

		$this->assertEquals( '<auth>', $msg[0] );
	}

	public function testFacilityLimited()
	{
		$this->_object = new MW_Logger_File( $this->_filename, MW_Logger_Abstract::ERR, array( 'test' ) );
		$this->_object->log( 'user auth', MW_Logger_Abstract::ERR, 'auth' );

		$this->assertEquals( '', file_get_contents( $this->_filename ) );
	}
}

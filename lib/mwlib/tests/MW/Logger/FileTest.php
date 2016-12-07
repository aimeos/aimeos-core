<?php

namespace Aimeos\MW\Logger;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $filename;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->filename = 'loggertest.log';
		$this->object = new \Aimeos\MW\Logger\File( $this->filename );
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
		$this->object->log( 'error' );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->filename ) );

		if( empty( $msg ) ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::ERR, $msg[3] );
		$this->assertEquals( 'error', $msg[4] );


		$this->setExpectedException('\\Aimeos\\MW\\Logger\\Exception');
		$this->object->log( 'wrong log level', -1);
	}

	public function testScalarLog()
	{
		$this->object->log( array ( 'scalar', 'errortest' ) );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->filename ) );

		if( empty( $msg ) ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::ERR, $msg[3] );
		$this->assertEquals( '["scalar","errortest"]', $msg[4] );
	}

	public function testLogCrit()
	{
		$this->object->log( 'critical', \Aimeos\MW\Logger\Base::CRIT );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->filename ) );

		if( empty( $msg ) ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( '<message>', $msg[0] );
		$this->assertEquals( 1, preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $msg[1] ) );
		$this->assertEquals( 1, preg_match( '/[0-9]{2}:[0-9]{2}:[0-9]{2}/', $msg[2] ) );
		$this->assertEquals( \Aimeos\MW\Logger\Base::CRIT, $msg[3] );
		$this->assertEquals( 'critical', $msg[4] );
	}

	public function testLogWarn()
	{
		$this->object->log( 'debug', \Aimeos\MW\Logger\Base::WARN );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$msg = file_get_contents( $this->filename );

		if( $msg !== '' ) {
			throw new \RuntimeException( 'Log record found but none expected' );
		}
	}

	public function testFacility()
	{
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$msg = explode( ' ', file_get_contents( $this->filename ) );

		if( empty( $msg ) ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( '<auth>', $msg[0] );
	}

	public function testFacilityLimited()
	{
		$this->object = new \Aimeos\MW\Logger\File( $this->filename, \Aimeos\MW\Logger\Base::ERR, array( 'test' ) );
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		$this->assertEquals( '', file_get_contents( $this->filename ) );
	}
}

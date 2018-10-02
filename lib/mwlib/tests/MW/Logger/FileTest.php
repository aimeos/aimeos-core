<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Logger;


class FileTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $filename;


	protected function setUp()
	{
		$this->filename = 'loggertest.log';
		$this->object = new \Aimeos\MW\Logger\File( $this->filename );
	}


	protected function tearDown()
	{
		if( file_exists( 'loggertest.log' ) ) {
			unlink( 'loggertest.log' );
		}
	}


	public function testLog()
	{
		$this->object->log( 'error' );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$lines = explode( PHP_EOL, file_get_contents( $this->filename ) );
		$msg = explode( ' ', $lines[0] );

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

		$lines = explode( PHP_EOL, file_get_contents( $this->filename ) );
		$msg = explode( ' ', $lines[0] );

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

		$lines = explode( PHP_EOL, file_get_contents( $this->filename ) );
		$msg = explode( ' ', $lines[0] );

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

		$this->assertFalse( file_exists( $this->filename ) );
	}


	public function testFacility()
	{
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		if( !file_exists( $this->filename ) ) {
			throw new \RuntimeException( 'No test file found' );
		}

		$lines = explode( PHP_EOL, file_get_contents( $this->filename ) );
		$msg = explode( ' ', $lines[0] );

		if( empty( $msg ) ) {
			throw new \RuntimeException( 'No log record found' );
		}

		$this->assertEquals( '<auth>', $msg[0] );
	}


	public function testFacilityLimited()
	{
		$this->object = new \Aimeos\MW\Logger\File( $this->filename, \Aimeos\MW\Logger\Base::ERR, array( 'test' ) );
		$this->object->log( 'user auth', \Aimeos\MW\Logger\Base::ERR, 'auth' );

		$this->assertFalse( file_exists( $this->filename ) );
	}
}

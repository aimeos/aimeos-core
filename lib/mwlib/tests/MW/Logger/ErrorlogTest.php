<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\Logger;


class ErrorlogTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::DEBUG );
	}


	protected function tearDown()
	{
		if( file_exists( 'error.log' ) ) {
			unlink( 'error.log' );
		}
	}


	public function testLog()
	{
		if( defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'Hiphop VM does not support ini settings yet' );
		}

		ini_set( "error_log", "error.log" );

		$this->object->log( 'error test' );
		$this->object->log( 'warning test', \Aimeos\MW\Logger\Base::WARN );
		$this->object->log( 'notice test', \Aimeos\MW\Logger\Base::NOTICE );
		$this->object->log( 'info test', \Aimeos\MW\Logger\Base::INFO );
		$this->object->log( 'debug test', \Aimeos\MW\Logger\Base::DEBUG );
		$this->object->log( array( 'scalar', 'test' ) );

		if( ( $content = file( 'error.log' ) ) === false ) {
			throw new \RuntimeException( 'Unable to open file "error.log"' );
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

		$this->object = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::DEBUG, array('test') );
		$this->object->log( 'info test', \Aimeos\MW\Logger\Base::INFO, 'info' );

		ini_restore( "error_log" );

		if( file_exists( 'error.log' ) ) {
			throw new \RuntimeException( 'File "error.log" should not be created' );
		}
	}


	public function testLogLevel()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Logger\\Exception' );
		$this->object->log( 'wrong loglevel test', -1 );
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Logger;


class ErrorlogTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::DEBUG );
	}


	protected function tearDown() : void
	{
		if( file_exists( 'error.log' ) ) {
			unlink( 'error.log' );
		}
	}


	public function testLog()
	{
		ini_set( "error_log", "error.log" );

		$this->object->log( 'error test' );
		$this->object->log( 'warning test', \Aimeos\MW\Logger\Base::WARN );
		$this->object->log( 'notice test', \Aimeos\MW\Logger\Base::NOTICE );
		$this->object->log( 'info test', \Aimeos\MW\Logger\Base::INFO );
		$this->object->log( 'debug test', \Aimeos\MW\Logger\Base::DEBUG );
		$this->object->log( array( 'scalar', 'test' ) );

		ini_restore( "error_log" );

		$this->assertFileExists( 'error.log', 'Unable to open file "error.log"' );

		foreach( file( 'error.log' ) as $line ) {
			$this->assertRegExp( '/\[[^\]]+\] <message> \[[^\]]+\] \[[^\]]+\] .+test/', $line, $line );
		}
	}


	public function testLogFacility()
	{
		ini_set( "error_log", "error.log" );

		$this->object = new \Aimeos\MW\Logger\Errorlog( \Aimeos\MW\Logger\Base::DEBUG, array( 'test' ) );
		$this->object->log( 'info test', \Aimeos\MW\Logger\Base::INFO, 'info' );

		ini_restore( "error_log" );

		$this->assertFileNotExists( 'error.log', 'File "error.log" should not be created' );
	}


	public function testLogLevel()
	{
		$this->expectException( \Aimeos\MW\Logger\Exception::class );
		$this->object->log( 'wrong loglevel test', -1 );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_Logger_ComposeTest extends PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$loggers[] = new MW_Logger_File( 'tmp/error1.log', MW_Logger_Abstract::ERR );
		$loggers[] = new MW_Logger_File( 'tmp/error2.log', MW_Logger_Abstract::INFO, array( 'test' ) );
		$loggers[] = new MW_Logger_File( 'tmp/error3.log', MW_Logger_Abstract::DEBUG );

		$this->object = new MW_Logger_Compose( $loggers );
	}


	protected function tearDown()
	{
		unlink( 'tmp/error1.log' );
		unlink( 'tmp/error2.log' );
		unlink( 'tmp/error3.log' );
	}


	public function testLog()
	{
		$this->object->log( 'warning test', MW_Logger_Abstract::WARN );

		$this->assertEquals( '', file_get_contents( 'tmp/error1.log' ) );
		$this->assertEquals( '', file_get_contents( 'tmp/error2.log' ) );
		$this->assertNotEquals( '', file_get_contents( 'tmp/error3.log' ) );
	}


	public function testLogFacility()
	{
		$this->object->log( 'warning test', MW_Logger_Abstract::WARN, 'test' );

		$this->assertEquals( '', file_get_contents( 'tmp/error1.log' ) );
		$this->assertNotEquals( '', file_get_contents( 'tmp/error2.log' ) );
		$this->assertNotEquals( '', file_get_contents( 'tmp/error3.log' ) );
	}
}

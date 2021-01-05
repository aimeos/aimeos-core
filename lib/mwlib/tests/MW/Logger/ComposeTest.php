<?php

namespace Aimeos\MW\Logger;


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class ComposeTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$loggers = array(
			new \Aimeos\MW\Logger\File( 'tmp/error1.log', \Aimeos\MW\Logger\Base::ERR ),
			new \Aimeos\MW\Logger\File( 'tmp/error2.log', \Aimeos\MW\Logger\Base::INFO, array( 'test' ) ),
			new \Aimeos\MW\Logger\File( 'tmp/error3.log', \Aimeos\MW\Logger\Base::DEBUG ),
		);

		$this->object = new \Aimeos\MW\Logger\Compose( $loggers );
	}


	protected function tearDown() : void
	{
		if( file_exists( 'tmp/error2.log' ) ) {
			unlink( 'tmp/error2.log' );
		}

		unlink( 'tmp/error3.log' );
	}


	public function testLog()
	{
		$this->object->log( 'warning test', \Aimeos\MW\Logger\Base::WARN );

		$this->assertNotEquals( '', file_get_contents( 'tmp/error3.log' ) );
	}


	public function testLogFacility()
	{
		$this->object->log( 'warning test', \Aimeos\MW\Logger\Base::WARN, 'test' );

		$this->assertNotEquals( '', file_get_contents( 'tmp/error2.log' ) );
		$this->assertNotEquals( '', file_get_contents( 'tmp/error3.log' ) );
	}
}

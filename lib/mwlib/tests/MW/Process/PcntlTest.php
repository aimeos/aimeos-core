<?php

namespace Aimeos\MW\Process;


class PcntlTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp()
	{
		if( function_exists( 'pcntl_fork' ) === false ) {
			$this->markTestSkipped( 'PCNTL extension not available' );
		}
	}


	public function testIsAvailable()
	{
		$object = new \Aimeos\MW\Process\Pcntl();
		$this->assertTrue( $object->isAvailable() );
	}


	public function testRun()
	{
		$object = new \Aimeos\MW\Process\Pcntl();
		$fcn = function() { sleep( 1 ); };

		$start = microtime( true );
		$return = $object->start( $fcn, [] )->start( $fcn, [] )->wait();
		$msec = ( microtime( true ) - $start );

		$this->assertInstanceOf( '\Aimeos\MW\Process\Iface', $return );
		$this->assertGreaterThan( 1, $msec );
		$this->assertLessThan( 2, $msec );
	}


	public function testRunException()
	{
		$fcn = function() { throw new \Exception(); };

		$object = new \Aimeos\MW\Process\Pcntl();

		$this->setExpectedException( '\Aimeos\MW\Process\Exception' );
		$object->start( $fcn, [], true )->wait();
	}
}

<?php

namespace Aimeos\MW\Process;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	public function testIsAvailable()
	{
		$object = new \Aimeos\MW\Process\None();
		$this->assertFalse( $object->isAvailable() );
	}


	public function testStart()
	{
		$object = new \Aimeos\MW\Process\None();

		$this->assertInstanceOf( \Aimeos\MW\Process\Iface::class, $object->start( function() {}, [] ) );
	}


	public function testWait()
	{
		$object = new \Aimeos\MW\Process\None();

		$this->assertInstanceOf( \Aimeos\MW\Process\Iface::class, $object->wait() );
	}
}

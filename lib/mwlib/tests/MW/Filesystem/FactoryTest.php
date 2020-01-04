<?php

namespace Aimeos\MW\Filesystem;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreate()
	{
		$result = Factory::create( array( 'adapter' => 'standard', 'basedir' => __DIR__ ) );
		$this->assertInstanceof( \Aimeos\MW\Filesystem\Iface::class, $result );
	}


	public function testCreateNoAdapter()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		Factory::create( array( 'basedir' => __DIR__ ) );
	}


	public function testCreateInvalid()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		Factory::create( array( 'adapter' => 'invalid' ) );
	}
}

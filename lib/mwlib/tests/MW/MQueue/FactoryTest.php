<?php

namespace Aimeos\MW\MQueue;


class FactoryTest extends \PHPUnit\Framework\TestCase
{
	public function testCreate()
	{
		$result = Factory::create( array( 'adapter' => 'None' ) );
		$this->assertInstanceof( \Aimeos\MW\MQueue\Iface::class, $result );
	}


	public function testCreateNoAdapter()
	{
		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		Factory::create( [] );
	}


	public function testCreateInvalid()
	{
		$this->expectException( \Aimeos\MW\MQueue\Exception::class );
		Factory::create( array( 'adapter' => 'invalid' ) );
	}
}

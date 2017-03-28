<?php

namespace Aimeos\MW\MQueue;


class FactoryTest extends \PHPUnit_Framework_TestCase
{
	public function testCreate()
	{
		$result = Factory::create( array( 'adapter' => 'None' ) );
		$this->assertInstanceof( '\Aimeos\MW\MQueue\Iface', $result );
	}


	public function testCreateNoAdapter()
	{
		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		Factory::create( [] );
	}


	public function testCreateInvalid()
	{
		$this->setExpectedException( '\Aimeos\MW\MQueue\Exception' );
		Factory::create( array( 'adapter' => 'invalid' ) );
	}
}

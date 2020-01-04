<?php

namespace Aimeos\MW\MQueue;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$config = array( 'db' => \TestHelperMw::getConfig()->get( 'resource/db' ) );
		$this->object = new \Aimeos\MW\MQueue\Standard( $config );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetQueue()
	{
		$queue = $this->object->getQueue( 'email' );

		$this->assertInstanceOf( \Aimeos\MW\MQueue\Queue\Iface::class, $queue );
		$this->assertSame( $queue, $this->object->getQueue( 'email' ) );
	}
}

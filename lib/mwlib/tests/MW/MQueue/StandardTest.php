<?php

namespace Aimeos\MW\MQueue;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$config = array( 'db' => \TestHelperMw::getConfig()->get( 'resource/db' ) );
		$this->object = new \Aimeos\MW\MQueue\Standard( $config );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetQueue()
	{
		$queue = $this->object->getQueue( 'email' );

		$this->assertInstanceOf( '\Aimeos\MW\MQueue\Queue\Iface', $queue );
		$this->assertSame( $queue, $this->object->getQueue( 'email' ) );
	}
}

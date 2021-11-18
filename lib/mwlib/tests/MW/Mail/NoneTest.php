<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

namespace Aimeos\MW\Mail;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Mail\None();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testCreate()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->create() );
	}


	public function testSend()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Iface::class, $this->object->send( $this->object->create() ) );
	}
}

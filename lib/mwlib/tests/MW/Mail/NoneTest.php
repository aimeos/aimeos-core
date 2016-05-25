<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */

namespace Aimeos\MW\Mail;


class NoneTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MW\Mail\None();
	}


	protected function tearDown()
	{
		unset($this->object);
	}


	public function testCreateMessage()
	{
		$this->assertInstanceOf( '\Aimeos\MW\Mail\Message\Iface', $this->object->createMessage() );
	}


	public function testSend()
	{
		$this->object->send( $this->object->createMessage() );
	}
}

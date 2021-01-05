<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */

namespace Aimeos\MW\Mail\Message;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Mail\Message\None();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAddFrom()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addFrom( 'test@example.com' ) );
	}


	public function testAddTo()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addTo( 'test@example.com' ) );
	}


	public function testAddCc()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addCc( 'test@example.com' ) );
	}


	public function testAddBcc()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addBcc( 'test@example.com' ) );
	}


	public function testAddReplyTo()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addReplyTo( 'test@example.com' ) );
	}


	public function testAddHeader()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addHeader( 'X-Generator', 'Aimeos' ) );
	}


	public function testSend()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->send() );
	}


	public function testSetSender()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->setSender( 'test@example.com' ) );
	}


	public function testSetSubject()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->setSubject( 'test' ) );
	}


	public function testSetBody()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->setBody( 'test' ) );
	}


	public function testSetBodyHtml()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->setBodyHtml( 'test' ) );
	}


	public function testAddAttachment()
	{
		$this->assertInstanceOf( \Aimeos\MW\Mail\Message\Iface::class, $this->object->addAttachment( 'test', 'mime', 'file' ) );
	}


	public function testEmbedAttachment()
	{
		$this->assertEquals( '', $this->object->embedAttachment( 'test', 'mime', 'file' ) );
	}
}

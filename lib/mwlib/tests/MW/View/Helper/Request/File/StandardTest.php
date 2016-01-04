<?php

namespace Aimeos\MW\View\Helper\Request\File;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MW\View\Helper\Request\File\Standard( __FILE__, 'test.txt', 1024, 'text/plain', 0 );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetStream()
	{
		$stream = $this->object->getStream();

		$this->assertInstanceOf( '\Psr\Http\Message\StreamInterface', $stream );

		$stream->close();
	}


	public function testMoveTo()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->moveTo( '/' );
	}


	public function testGetSize()
	{
		$this->assertEquals( 1024, $this->object->getSize() );
	}


	public function testGetError()
	{
		$this->assertEquals( 0, $this->object->getError() );
	}


	public function testGetClientFilename()
	{
		$this->assertEquals( 'test.txt', $this->object->getClientFilename() );
	}


	public function testGetClientMediaType()
	{
		$this->assertEquals( 'text/plain', $this->object->getClientMediaType() );
	}

}

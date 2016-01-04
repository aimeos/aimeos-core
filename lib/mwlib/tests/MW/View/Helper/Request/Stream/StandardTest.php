<?php

namespace Aimeos\MW\View\Helper\Request\Stream;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new \Aimeos\MW\View\Helper\Request\Stream\Standard( -1 );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testToString()
	{
		$object = new \Aimeos\MW\View\Helper\Request\Stream\Standard( fopen( __FILE__, 'r' ) );

		$this->assertStringStartsWith( '<?php', (string) $object );
	}


	public function testClose()
	{
		$object = new \Aimeos\MW\View\Helper\Request\Stream\Standard( fopen( __FILE__, 'r' ) );
		$object->close();
	}


	public function testDetach()
	{
		$this->assertEquals( -1, $this->object->detach() );
	}


	public function testGetSize()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->getSize();
	}


	public function testTell()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->tell();
	}


	public function testEof()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->eof();
	}


	public function testIsSeekable()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->isSeekable();
	}


	public function testSeek()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->seek( 0 );
	}


	public function testRewind()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->rewind();
	}


	public function testIsWritable()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->isWritable();
	}


	public function testWrite()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->write( 'test' );
	}


	public function testIsReadable()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->isReadable();
	}


	public function testRead()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->read( 0 );
	}


	public function testGetContents()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->getContents();
	}


	public function testGetMetadata()
	{
		$this->setExpectedException( '\RuntimeException' );
		$this->object->getMetadata();
	}

}

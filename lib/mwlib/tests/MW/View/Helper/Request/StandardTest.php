<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Request;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $request;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->request = $this->getMockBuilder( \Psr\Http\Message\ServerRequestInterface::class )->getMock();
		$this->object = new \Aimeos\MW\View\Helper\Request\Standard( $view, $this->request, '127.0.0.1', 'test' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->request );
	}


	public function testTransform()
	{
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Request\Iface::class, $this->object->transform() );
	}


	public function testGetClientAddress()
	{
		$this->assertEquals( '127.0.0.1', $this->object->transform()->getClientAddress() );
	}


	public function testGetTarget()
	{
		$this->assertEquals( 'test', $this->object->transform()->getTarget() );
	}


	public function testGetProtocolVersion()
	{
		$this->request->expects( $this->once() )->method( 'getProtocolVersion' )
			->will( $this->returnValue( '1.0' ) );

		$this->assertEquals( '1.0', $this->object->getProtocolVersion() );
	}


	public function testWithProtocolVersion()
	{
		$this->request->expects( $this->once() )->method( 'withProtocolVersion' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withProtocolVersion( '1.0' ) );
	}


	public function testGetHeaders()
	{
		$this->request->expects( $this->once() )->method( 'getHeaders' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getHeaders() );
	}


	public function testHasHeader()
	{
		$this->request->expects( $this->once() )->method( 'hasHeader' )
			->will( $this->returnValue( true ) );

		$this->assertEquals( true, $this->object->hasHeader( 'test' ) );
	}


	public function testGetHeader()
	{
		$this->request->expects( $this->once() )->method( 'getHeader' )
			->will( $this->returnValue( 'value' ) );

		$this->assertEquals( 'value', $this->object->getHeader( 'test' ) );
	}


	public function testGetHeaderLine()
	{
		$this->request->expects( $this->once() )->method( 'getHeaderLine' )
			->will( $this->returnValue( 'value' ) );

		$this->assertEquals( 'value', $this->object->getHeaderLine( 'test' ) );
	}


	public function testWithHeader()
	{
		$this->request->expects( $this->once() )->method( 'withHeader' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withHeader( 'test', 'value' ) );
	}


	public function testWithAddedHeader()
	{
		$this->request->expects( $this->once() )->method( 'withAddedHeader' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withAddedHeader( 'test', 'value' ) );
	}


	public function testWithoutHeader()
	{
		$this->request->expects( $this->once() )->method( 'withoutHeader' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withoutHeader( 'test' ) );
	}


	public function testGetBody()
	{
		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();

		$this->request->expects( $this->once() )->method( 'getBody' )
			->will( $this->returnValue( $stream ) );

		$this->assertEquals( $stream, $this->object->getBody() );
	}


	public function testWithBody()
	{
		$stream = $this->getMockBuilder( \Psr\Http\Message\StreamInterface::class )->getMock();

		$this->request->expects( $this->once() )->method( 'withBody' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withBody( $stream ) );
	}


	public function testGetRequestTarget()
	{
		$this->request->expects( $this->once() )->method( 'getRequestTarget' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->getRequestTarget() );
	}


	public function testWithRequestTarget()
	{
		$this->request->expects( $this->once() )->method( 'withRequestTarget' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withRequestTarget( 'test' ) );
	}


	public function testGetMethod()
	{
		$this->request->expects( $this->once() )->method( 'getMethod' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->getMethod() );
	}


	public function testWithMethod()
	{
		$this->request->expects( $this->once() )->method( 'withMethod' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withMethod( 'test' ) );
	}


	public function testGetUri()
	{
		$uri = $this->getMockBuilder( \Psr\Http\Message\UriInterface::class )->getMock();

		$this->request->expects( $this->once() )->method( 'getUri' )
			->will( $this->returnValue( $uri ) );

		$this->assertEquals( $uri, $this->object->getUri() );
	}


	public function testWithUri()
	{
		$uri = $this->getMockBuilder( \Psr\Http\Message\UriInterface::class )->getMock();

		$this->request->expects( $this->once() )->method( 'withUri' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withUri( $uri, false ) );
	}


	public function testGetServerParams()
	{
		$this->request->expects( $this->once() )->method( 'getServerParams' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getServerParams() );
	}


	public function testGetCookieParams()
	{
		$this->request->expects( $this->once() )->method( 'getCookieParams' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getCookieParams() );
	}


	public function testWithCookieParams()
	{
		$this->request->expects( $this->once() )->method( 'withCookieParams' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withCookieParams( [] ) );
	}


	public function testGetQueryParams()
	{
		$this->request->expects( $this->once() )->method( 'getQueryParams' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getQueryParams() );
	}


	public function testWithQueryParams()
	{
		$this->request->expects( $this->once() )->method( 'withQueryParams' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withQueryParams( [] ) );
	}


	public function testGetUploadedFiles()
	{
		$this->request->expects( $this->once() )->method( 'getUploadedFiles' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getUploadedFiles() );
	}


	public function testWithUploadedFiles()
	{
		$this->request->expects( $this->once() )->method( 'withUploadedFiles' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withUploadedFiles( [] ) );
	}


	public function testGetParsedBody()
	{
		$this->request->expects( $this->once() )->method( 'getParsedBody' )
			->will( $this->returnValue( 'test' ) );

		$this->assertEquals( 'test', $this->object->getParsedBody() );
	}


	public function testWithParsedBody()
	{
		$this->request->expects( $this->once() )->method( 'withParsedBody' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withParsedBody( array( 'test' ) ) );
	}


	public function testGetAttributes()
	{
		$this->request->expects( $this->once() )->method( 'getAttributes' )
			->will( $this->returnValue( [] ) );

		$this->assertEquals( [], $this->object->getAttributes() );
	}


	public function testGetAttribute()
	{
		$this->request->expects( $this->once() )->method( 'getAttribute' )
			->will( $this->returnValue( 'value' ) );

		$this->assertEquals( 'value', $this->object->getAttribute( 'test', 'default' ) );
	}


	public function testWithAttribute()
	{
		$this->request->expects( $this->once() )->method( 'withAttribute' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withAttribute( 'test', 'value' ) );
	}


	public function testWithoutAttribute()
	{
		$this->request->expects( $this->once() )->method( 'withoutAttribute' )
			->will( $this->returnValue( $this->request ) );

		$this->assertEquals( $this->object, $this->object->withoutAttribute( 'test' ) );
	}
}

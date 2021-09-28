<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\Controller\Common\Media;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;


	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );

		$this->context = \TestHelperCntl::getContext();
		$this->object = new \Aimeos\Controller\Common\Media\Standard( $this->context );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
		unset( $this->object );
	}


	public function testAdd()
	{
		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Media\Standard::class )
			->setMethods( array( 'checkFileUpload', 'store' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'checkFileUpload' );
		$object->expects( $this->exactly( 2 ) )->method( 'store' );

		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();

		$file->expects( $this->exactly( 3 ) )->method( 'getClientFilename' )
			->will( $this->returnValue( 'test.gif' ) );

		$file->expects( $this->once() )->method( 'getStream' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/testfiles/test.gif' ) ) );


		$item = \Aimeos\MShop::create( $this->context, 'media' )->create()->setDomain( 'product' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $object->add( $item, $file ) );
	}


	public function testAddPreview()
	{
		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Media\Standard::class )
			->setConstructorArgs( [$this->context] )
			->setMethods( ['checkFileUpload'] )
			->getMock();

		$object->expects( $this->once() )->method( 'checkFileUpload' );

		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();

		$file->expects( $this->exactly( 3 ) )->method( 'getClientFilename' )
			->will( $this->returnValue( 'test.gif' ) );

		$file->expects( $this->once() )->method( 'getStream' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/testfiles/test.gif' ) ) );


		$item = \Aimeos\MShop::create( $this->context, 'media' )->create()->setDomain( 'product' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $object->add( $item, $file ) );
	}


	public function testAddBinary()
	{
		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Media\Standard::class )
			->setMethods( array( 'checkFileUpload', 'store' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'checkFileUpload' );
		$object->expects( $this->once() )->method( 'store' );

		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();

		$file->expects( $this->exactly( 2 ) )->method( 'getClientFilename' )
			->will( $this->returnValue( 'test.gif' ) );

		$file->expects( $this->once() )->method( 'getStream' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/testfiles/test.pdf' ) ) );

		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $object->add( $item, $file ) );
	}


	public function testCopy()
	{
		$fsm = $this->getMockBuilder( \Aimeos\MW\Filesystem\Manager\Standard::class )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( \Aimeos\MW\Filesystem\Standard::class )
			->setMethods( array( 'has', 'copy' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'has' )
			->will( $this->returnValue( true ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'copy' );

		$this->context->setFilesystemManager( $fsm );

		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();
		$item->setPreview( 'test' )->setUrl( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $this->object->copy( $item ) );
	}


	public function testDelete()
	{
		$fsm = $this->getMockBuilder( \Aimeos\MW\Filesystem\Manager\Standard::class )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( \Aimeos\MW\Filesystem\Standard::class )
			->setMethods( array( 'has', 'rm' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'has' )
			->will( $this->returnValue( true ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'rm' );

		$this->context->setFilesystemManager( $fsm );

		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();
		$item->setPreview( 'test' )->setUrl( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $this->object->delete( $item ) );
	}


	public function testDeleteMimeicon()
	{
		$this->context->getConfig()->set( 'controller/common/media/mimeicon/directory', 'path/to/mimeicons' );

		$fsm = $this->getMockBuilder( \Aimeos\MW\Filesystem\Manager\Standard::class )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( \Aimeos\MW\Filesystem\Standard::class )
			->setMethods( array( 'has', 'rm' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->exactly( 2 ) )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->exactly( 1 ) )->method( 'has' )
			->will( $this->returnValue( true ) );

		$fs->expects( $this->exactly( 1 ) )->method( 'rm' );

		$this->context->setFilesystemManager( $fsm );

		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();
		$item->setPreview( 'path/to/mimeicons/application/test.png' )->setUrl( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $this->object->delete( $item ) );
	}


	public function testScale()
	{
		$this->context->getConfig()->set( 'controller/common/media/files/scale', true );

		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/';
		if( !is_dir( $dest ) ) { mkdir( $dest, 0755, true ); }
		copy( __DIR__ . '/testfiles/test.gif', $dest . 'test.gif' );

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Media\Standard::class )
			->setMethods( array( 'getFileContent', 'store' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getFileContent' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/testfiles/test.png' ) ) );

		$object->expects( $this->exactly( 1 ) )->method( 'store' );


		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();
		$item->setPreview( 'preview.gif' )->setUrl( 'test.gif' )->setDomain( 'product' );

		$result = $object->scale( $item, 'fs-media', 1 );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $result );
		$this->assertEquals( 'test.gif', $result->getUrl() );
		$this->assertNotEquals( 'preview.gif', $result->getPreview() );
	}


	public function testScaleLegacy()
	{
		$this->context->getConfig()->set( 'controller/common/media/files/scale', true );

		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/';
		if( !is_dir( $dest ) ) { mkdir( $dest, 0755, true ); }
		copy( __DIR__ . '/testfiles/test.gif', $dest . 'test.gif' );

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Media\Standard::class )
			->setMethods( array( 'getFileContent', 'store' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getFileContent' )
			->will( $this->returnValue( file_get_contents( __DIR__ . '/testfiles/test.png' ) ) );

		$object->expects( $this->exactly( 1 ) )->method( 'store' );


		$item = \Aimeos\MShop::create( $this->context, 'media' )->create();
		$item->setPreview( 'preview.gif' )->setUrl( 'test.gif' )->setDomain( 'product' );

		$result = $object->scale( $item, 'fs-media', true );

		$this->assertInstanceOf( \Aimeos\MShop\Media\Item\Iface::class, $result );
		$this->assertEquals( 'test.gif', $result->getUrl() );
		$this->assertNotEquals( 'preview.gif', $result->getPreview() );
	}


	public function testCheckFileUploadOK()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->once() )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_OK ) );

		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadSize()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_INI_SIZE ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadPartitial()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_PARTIAL ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadNoFile()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_NO_FILE ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadNoTmpdir()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_NO_TMP_DIR ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadCantWrite()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_CANT_WRITE ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadExtension()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_EXTENSION ) );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadException()
	{
		$file = $this->getMockBuilder( \Psr\Http\Message\UploadedFileInterface::class )->getMock();

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testGetFileContent()
	{
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/';
		if( !is_dir( $dest ) ) { mkdir( $dest, 0755, true ); }
		copy( __DIR__ . '/testfiles/test.gif', $dest . 'test.gif' );

		$result = $this->access( 'getFileContent' )->invokeArgs( $this->object, array( 'test.gif', 'fs-media' ) );

		$this->assertNotEquals( '', $result );
	}


	public function testGetFileContentHttp()
	{
		$url = 'https://aimeos.org/fileadmin/logos/favicon.png';
		$result = $this->access( 'getFileContent' )->invokeArgs( $this->object, array( $url, 'fs-media' ) );

		$this->assertNotEquals( '', $result );
	}


	public function testGetFileContentException()
	{
		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'getFileContent' )->invokeArgs( $this->object, array( '', 'fs-media' ) );
	}


	public function testGetFilePathOctet()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'application/octet-stream' ) );
		$this->assertNotEquals( '.', substr( $result, -4, 1 ) );
	}


	public function testGetFilePathPDF()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'application/pdf' ) );
		$this->assertEquals( '.pdf', substr( $result, -4 ) );
	}


	public function testGetFilePathGIF()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'image/gif' ) );
		$this->assertEquals( '.gif', substr( $result, -4 ) );
	}


	public function testGetFilePathJPEG()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'image/jpeg' ) );
		$this->assertEquals( '.jpg', substr( $result, -4 ) );
	}


	public function testGetFilePathPNG()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'image/png' ) );
		$this->assertEquals( '.png', substr( $result, -4 ) );
	}


	public function testGetFilePathTIFF()
	{
		$result = $this->access( 'getFilePath' )->invokeArgs( $this->object, array( '', 'files', 'image/tiff' ) );
		$this->assertEquals( '.tif', substr( $result, -4 ) );
	}


	public function testGetMediaFile()
	{
		$result = $this->access( 'getMediaFile' )->invokeArgs( $this->object, array( __FILE__ ) );
		$this->assertInstanceOf( \Aimeos\MW\Media\Iface::class, $result );
	}


	public function testGetMimeIcon()
	{
		file_exists( 'tmp/image' ) ?: mkdir( 'tmp/image' );
		touch( 'tmp/image/jpeg.png' );

		$this->context->getConfig()->set( 'controller/common/media/mimeicon/directory', 'tmp' );
		$result = $this->access( 'getMimeIcon' )->invokeArgs( $this->object, array( 'image/jpeg' ) );
		$this->assertStringContainsString( 'tmp/image/jpeg.png', $result );
	}


	public function testGetMimeIconNoConfig()
	{
		$this->context->getConfig()->set( 'controller/common/media/mimeicon/directory', '' );
		$result = $this->access( 'getMimeIcon' )->invokeArgs( $this->object, array( 'image/jpeg' ) );
		$this->assertEquals( '', $result );
	}


	public function testGetMimeType()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/testfiles/test.png' );

		$result = $this->access( 'getMimeType' )->invokeArgs( $this->object, array( $file, 'files' ) );
		$this->assertEquals( 'image/png', $result );
	}


	public function testGetMimeTypeNotAllowed()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/testfiles/test.gif' );
		$this->context->getConfig()->set( 'controller/common/media/files/allowedtypes', array( 'image/jpeg' ) );

		$result = $this->access( 'getMimeType' )->invokeArgs( $this->object, array( $file, 'files' ) );
		$this->assertEquals( 'image/jpeg', $result );
	}


	public function testGetMimeTypeNoTypes()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/testfiles/test.gif' );
		$this->context->getConfig()->set( 'controller/common/media/files/allowedtypes', [] );

		$this->expectException( \Aimeos\Controller\Common\Exception::class );
		$this->access( 'getMimeType' )->invokeArgs( $this->object, array( $file, 'files' ) );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\Controller\Common\Media\Standard::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}

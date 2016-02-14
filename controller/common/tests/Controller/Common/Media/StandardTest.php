<?php

namespace Aimeos\Controller\Common\Media;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $context;
	private $object;


	protected function setUp()
	{
		$this->context = \TestHelperCntl::getContext();
		$this->object = new \Aimeos\Controller\Common\Media\Standard( $this->context );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testAdd()
	{
		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Media\Standard' )
			->setMethods( array( 'checkFileUpload', 'deleteFile', 'getTempFileName', 'storeImage' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'checkFileUpload' );
		$object->expects( $this->once() )->method( 'deleteFile' );
		$object->expects( $this->exactly( 2 ) )->method( 'storeImage' );
		$object->expects( $this->once() )->method( 'getTempFileName' )
			->will( $this->returnValue( __DIR__ . '/testfiles/test.gif' ) );

		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->once() )->method( 'moveTo' );


		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'media' )->createItem();

		$object->add( $item, $file );
	}


	public function testAddBinary()
	{
		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Media\Standard' )
			->setMethods( array( 'checkFileUpload', 'deleteFile', 'getTempFileName', 'storeFile' ) )
			->setConstructorArgs( array( $this->context ) )
			->getMock();

		$object->expects( $this->once() )->method( 'checkFileUpload' );
		$object->expects( $this->once() )->method( 'deleteFile' );
		$object->expects( $this->once() )->method( 'storeFile' );
		$object->expects( $this->once() )->method( 'getTempFileName' )
			->will( $this->returnValue( __DIR__ . '/testfiles/test.pdf' ) );

		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->once() )->method( 'moveTo' );


		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'media' )->createItem();

		$object->add( $item, $file );
	}


	public function testDelete()
	{
		$fsm = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Manager\Standard' )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Standard' )
			->setMethods( array( 'has', 'rm' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'has' )
			->will( $this->returnValue( true ) );

		$fs->expects( $this->exactly( 2 ) )->method( 'rm' );


		$item = \Aimeos\MShop\Factory::createManager( $this->context, 'media' )->createItem();
		$item->setPreview( 'test' );
		$item->setUrl( 'test' );

		$this->context->setFilesystemManager( $fsm );
		$this->object->delete( $item );
	}


	public function testCheckFileUploadOK()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->once() )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_OK ) );

		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadSize()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_INI_SIZE ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadPartitial()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_PARTIAL ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadNoFile()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_NO_FILE ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadNoTmpdir()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_NO_TMP_DIR ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadCantWrite()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_CANT_WRITE ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadExtension()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );
		$file->expects( $this->exactly( 2 ) )->method( 'getError' )->will( $this->returnValue( UPLOAD_ERR_EXTENSION ) );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testCheckFileUploadException()
	{
		$file = $this->getMock( '\Psr\Http\Message\UploadedFileInterface' );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'checkFileUpload' )->invokeArgs( $this->object, array( $file ) );
	}


	public function testGetFileExtensionPDF()
	{
		$result = $this->access( 'getFileExtension' )->invokeArgs( $this->object, array( 'application/pdf' ) );
		$this->assertEquals( '.pdf', $result );
	}


	public function testGetFileExtensionGIF()
	{
		$result = $this->access( 'getFileExtension' )->invokeArgs( $this->object, array( 'image/gif' ) );
		$this->assertEquals( '.gif', $result );
	}


	public function testGetFileExtensionJPEG()
	{
		$result = $this->access( 'getFileExtension' )->invokeArgs( $this->object, array( 'image/jpeg' ) );
		$this->assertEquals( '.jpg', $result );
	}


	public function testGetFileExtensionPNG()
	{
		$result = $this->access( 'getFileExtension' )->invokeArgs( $this->object, array( 'image/png' ) );
		$this->assertEquals( '.png', $result );
	}


	public function testGetFileExtensionTIFF()
	{
		$result = $this->access( 'getFileExtension' )->invokeArgs( $this->object, array( 'image/tiff' ) );
		$this->assertEquals( '.tif', $result );
	}


	public function testGetMediaFile()
	{
		$result = $this->access( 'getMediaFile' )->invokeArgs( $this->object, array( __FILE__ ) );
		$this->assertInstanceOf( '\Aimeos\MW\Media\Iface', $result );
	}


	public function testGetMimeIcon()
	{
		$result = $this->access( 'getMimeIcon' )->invokeArgs( $this->object, array( 'image/jpeg' ) );
		$this->assertContains( 'tmp/media/mimeicons/image/jpeg.png', $result );
	}


	public function testGetMimeIconNoConfig()
	{
		$this->context->getConfig()->set( 'controller/common/media/standard/mimeicon/directory', '' );
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
		$this->context->getConfig()->set( 'controller/common/media/standard/files/allowedtypes', array( 'image/jpeg' ) );

		$result = $this->access( 'getMimeType' )->invokeArgs( $this->object, array( $file, 'files' ) );
		$this->assertEquals( 'image/jpeg', $result );
	}


	public function testGetMimeTypeNoTypes()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/testfiles/test.gif' );
		$this->context->getConfig()->set( 'controller/common/media/standard/files/allowedtypes', array() );

		$this->setExpectedException( '\Aimeos\Controller\Common\Exception' );
		$this->access( 'getMimeType' )->invokeArgs( $this->object, array( $file, 'files' ) );
	}


	public function testScaleImage()
	{
		$file = \Aimeos\MW\Media\Factory::get( __DIR__ . '/testfiles/test.gif' );

		$this->access( 'scaleImage' )->invokeArgs( $this->object, array( $file, 'files' ) );
	}


	public function testStoreFile()
	{
		$fsm = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Manager\Standard' )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Standard' )
			->setMethods( array( 'writef' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->once() )->method( 'writef' );

		$this->context->setFilesystemManager( $fsm );


		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/';
		if( !is_dir( $dest ) ) { mkdir( $dest, 0755, true ); }
		copy( __DIR__ . '/testfiles/test.gif', $dest . 'test.gif' );

		$file = \Aimeos\MW\Media\Factory::get( $dest . 'test.gif' );

		$this->access( 'storeFile' )->invokeArgs( $this->object, array( $file, 'files', 'test', 'fs-media' ) );
	}


	public function testStoreImage()
	{
		$fsm = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Manager\Standard' )
			->setMethods( array( 'get' ) )
			->disableOriginalConstructor()
			->getMock();

		$fs = $this->getMockBuilder( '\Aimeos\MW\Filesystem\Standard' )
			->setMethods( array( 'writef' ) )
			->disableOriginalConstructor()
			->getMock();

		$fsm->expects( $this->once() )->method( 'get' )
			->will( $this->returnValue( $fs ) );

		$fs->expects( $this->once() )->method( 'writef' );

		$this->context->setFilesystemManager( $fsm );


		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . '/tmp/';
		if( !is_dir( $dest ) ) { mkdir( $dest, 0755, true ); }
		copy( __DIR__ . '/testfiles/test.gif', $dest . 'test.gif' );

		$file = \Aimeos\MW\Media\Factory::get( $dest . 'test.gif' );
		$result = $this->access( 'storeImage' )->invokeArgs( $this->object, array( $file, 'files', 'test', 'fs-media' ) );

		$this->assertEquals( 'files/t/e/test.gif', $result );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\Controller\Common\Media\Standard' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}
}

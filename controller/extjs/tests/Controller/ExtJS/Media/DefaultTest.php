<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_ExtJS_Media_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;
	private $directory;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$this->object = new Controller_ExtJS_Media_Default( $context );

		$tempdir = $context->getConfig()->get( 'controller/extjs/media/default/upload/directory', 'tmp/media' );
		$this->directory = PATH_TESTS . DIRECTORY_SEPARATOR . $tempdir;
		$testfiledir = dirname( __FILE__ ) . '/testfiles';

		if( !is_dir( $this->directory ) && mkdir( $this->directory, 0755, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create directory "%1%s"', $this->directory ) );
		}
		exec( sprintf( 'cp -r -f %1$s %2$s', escapeshellarg( $testfiledir ), escapeshellarg( $this->directory ) ) );
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

		exec( 'rm -rf ' . escapeshellarg( $this->directory ) );
	}


	public function testSearchItems()
	{
		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'media.label' => 'cn_colombie_' ) ) ) ),
			'sort' => 'media.label',
			'dir' => 'ASC',
			'start' => 0,
			'limit' => 1,
		);

		$result = $this->object->searchItems( $params );

		$this->assertEquals( 1, count( $result['items'] ) );
		$this->assertEquals( 6, $result['total'] );
		$this->assertEquals( 'de', $result['items'][0]->{'media.languageid'} );
	}


	public function testSaveDeleteItem()
	{
		$manager = MShop_Media_Manager_Factory::createManager( TestHelper::getContext() );
		$typeManager = $manager->getSubManager( 'type' );
		$criteria = $typeManager->createSearch();
		$criteria->setSlice( 0, 1 );
		$result = $typeManager->searchItems( $criteria );

		if( ( $type = reset( $result ) ) === false ) {
			throw new Exception( 'No type item found' );
		}

		$saveParams = (object) array(
			'site' => 'unittest',
			'items' => (object) array(
				'media.label' => 'controller test media',
				'media.domain' => 'attribute',
				'media.typeid' => $type->getId(),
				'media.languageid' => 'de',
				'media.url' => '/test/test.jpg',
				'media.mimetype' => 'image/jpeg',
				'media.status' => 0,
			),
		);

		$searchParams = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '==' => (object) array( 'media.label' => 'controller test media' ) ) ) )
		);

		$saved = $this->object->saveItems( $saveParams );
		$searched = $this->object->searchItems( $searchParams );

		$deleteParams = (object) array( 'site' => 'unittest', 'items' => $saved['items']->{'media.id'} );
		$this->object->deleteItems( $deleteParams );
		$result = $this->object->searchItems( $searchParams );

		$this->assertInternalType( 'object', $saved['items'] );
		$this->assertNotNull( $saved['items']->{'media.id'} );
		$this->assertEquals( $saved['items']->{'media.id'}, $searched['items'][0]->{'media.id'} );
		$this->assertEquals( $saved['items']->{'media.typeid'}, $searched['items'][0]->{'media.typeid'} );
		$this->assertEquals( $saved['items']->{'media.domain'}, $searched['items'][0]->{'media.domain'} );
		$this->assertEquals( $saved['items']->{'media.languageid'}, $searched['items'][0]->{'media.languageid'} );
		$this->assertEquals( $saved['items']->{'media.label'}, $searched['items'][0]->{'media.label'} );
		$this->assertEquals( $saved['items']->{'media.url'}, $searched['items'][0]->{'media.url'} );
		$this->assertEquals( $saved['items']->{'media.mimetype'}, $searched['items'][0]->{'media.mimetype'} );
		$this->assertEquals( $saved['items']->{'media.status'}, $searched['items'][0]->{'media.status'} );

		$this->assertEquals( 1, count( $searched['items'] ) );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testUploadJpeg()
	{
		$_FILES['unittest'] = array(
			'name' => 'test-jpeg.jpg',
			'tmp_name' => $this->directory . '/testfiles/test.jpeg',
			'error' => UPLOAD_ERR_OK,
		);

		$mediaItem = $this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.preview'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.preview'} );
	}

	public function testUploadPdf()
	{
		$_FILES['unittest'] = array(
			'name' => 'test-pdf.pdf',
			'tmp_name' => $this->directory . '/testfiles/test.pdf',
			'error' => UPLOAD_ERR_OK,
		);

		$mediaItem = $this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} );

		// No mime icons in file system available, so it will be unknown.png
		$this->assertEquals( 'tmp/media/mimeicons/unknown.png', $mediaItem->{'media.preview'} );
	}

	public function testUploadBinary()
	{
		$_FILES['unittest'] = array(
			'name' => 'test-binary.bin',
			'tmp_name' => $this->directory . '/testfiles/test.bin',
			'error' => UPLOAD_ERR_OK,
		);

		$mediaItem = $this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} );

		$this->assertEquals( 'tmp/media/mimeicons/unknown.png', $mediaItem->{'media.preview'} );
	}


	public function testUploadItemException()
	{
		$_FILES = array();
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testUploadItemExceptionWithEnableCheck()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/media/default/enablecheck', true );

		$_FILES['unittest'] = array(
			'name' => 'test-binary.bin',
			'tmp_name' => $this->directory . '/testfiles/test.bin',
			'error' => UPLOAD_ERR_OK,
		);

		$object = new Controller_ExtJS_Media_Default( $context );

		$this->setExpectedException( 'Controller_ExtJS_Exception' ); // not a real file upload
		$object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testProtectedCreateImageExceptionByUploadItem()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/media/default/upload/directory', null );
		$object = new Controller_ExtJS_Media_Default( $context );

		$_FILES['unittest'] = array(
			'name' => 'test-jpeg.jpg',
			'tmp_name' => $this->directory . '/testfiles/test.jpeg',
			'error' => UPLOAD_ERR_OK,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' ); // no upload directory
		$object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testProtectedGetMimeIconEmptyByUploadItem()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/media/default/mimeicon/directory', null );

		$object = new Controller_ExtJS_Media_Default( $context );

		$_FILES['unittest'] = array(
			'name' => 'test-binary.bin',
			'tmp_name' => $this->directory . '/testfiles/test.bin',
			'error' => UPLOAD_ERR_OK,
		);

		$mediaItem = $object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} );

		$this->assertEquals( '', $mediaItem->{'media.preview'} );
	}



	public function testProtectedGetAbsoluteDirectoryEmptyByUploadItem()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/media/default/basedir', null );
		$object = new Controller_ExtJS_Media_Default( $context );

		$_FILES['unittest'] = array(
			'name' => 'test-binary.bin',
			'tmp_name' => $this->directory . '/testfiles/test.bin',
			'error' => UPLOAD_ERR_OK,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

	}


	public function testProtectedGetAbsoluteDirectoryErrorByUploadItem()
	{
		$context = TestHelper::getContext();

		$context->getConfig()->set( 'controller/extjs/media/default/basedir', '/root/' );
		$context->getConfig()->set( 'controller/extjs/media/default/mimeicon/directory', '/2/' );

		$object = new Controller_ExtJS_Media_Default( $context );

		$_FILES['unittest'] = array(
			'name' => 'test-binary.bin',
			'tmp_name' => $this->directory . '/testfiles/test.bin',
			'error' => UPLOAD_ERR_OK,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testProtectedGetFileExtensionNoExtensionByUploadBinary()
	{
		$_FILES['unittest'] = array(
			'name' => 'testbin',
			'tmp_name' => $this->directory . '/testfiles/testbin',
			'error' => UPLOAD_ERR_OK,
		);

		$mediaItem = $this->object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );

		$this->assertTrue( is_file( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} ) );
		unlink( PATH_TESTS . DIRECTORY_SEPARATOR . $mediaItem->{'media.url'} );

		$this->assertEquals( 'tmp/media/mimeicons/unknown.png', $mediaItem->{'media.preview'} );
	}


	public function testProtectedCopyFileExceptionByUploadBinary()
	{
		$context = TestHelper::getContext();
		$context->getConfig()->set( 'controller/extjs/media/default/upload/directory', null );

		$object = new Controller_ExtJS_Media_Default( $context );

		$_FILES['unittest'] = array(
			'name' => 'testbin',
			'tmp_name' => $this->directory . '/testfiles/testbin',
			'error' => UPLOAD_ERR_OK,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadItem( (object) array( 'site' => 'unittest', 'domain' => 'product' ) );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Media.uploadItem' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "string", "name" => "domain", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.deleteItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.saveItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				"returns" => "array",
			),
			'Media.searchItems' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "condition", "optional" => true ),
					array( "type" => "integer", "name" => "start", "optional" => true ),
					array( "type" => "integer", "name" => "limit", "optional" => true ),
					array( "type" => "string", "name" => "sort", "optional" => true ),
					array( "type" => "string", "name" => "dir", "optional" => true ),
					array( "type" => "array", "name" => "options", "optional" => true ),
				),
				"returns" => "array",
			),
			'Media.init' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
			'Media.finish' => array(
				'parameters' => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
				),
				'returns' => 'array',
			),
		);

		$actual = $this->object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}


	public function testFinish()
	{
		$result = $this->object->finish( (object) array( 'site' => 'unittest', 'items' => -1 ) );

		$this->assertEquals( array( 'success' => true ), $result );
	}
}

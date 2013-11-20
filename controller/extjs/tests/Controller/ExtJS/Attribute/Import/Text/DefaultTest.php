<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Attribute_Import_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_testdir;
	private $_testfile;
	private $_context;

	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Attribute_Import_Text_DefaultTest' );
		$result = PHPUnit_TextUI_TestRunner::run( $suite );
	}


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();

		$this->_testdir = $this->_context->getConfig()->get( 'controller/extjs/attribute/import/text/default/uploaddir', './tmp' );
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Attribute_Import_Text_Default( $this->_context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		if ( file_exists( $this->_testfile )) {
			unlink( $this->_testfile );
		}
		$this->_object = null;
	}


	public function testGetServiceDescription()
	{
		$desc = $this->_object->getServiceDescription();
		$this->assertInternalType( 'array', $desc );
		$this->assertEquals( 2, count( $desc['Attribute_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Attribute_Import_Text.importFile'] ) );
	}


	public function testImportFromCSVFile()
	{
		$data[] = '"en","color","white","default","name","","unittest: white"'."\n";
		$data[] = '"en","color","blue","default","name","","unittest: blue"' ."\n";
		$data[] = '"en","color","red","default","name","","unittest: red"'."\n";
		$data[] = '"en","size","l","default","name","","unittest: l"'."\n";
		$data[] = '"en","size","xl","default","name","","unittest: xl"'."\n";
		$data[] = '"en","size","xxl","default","name","","unittest: xxl"'."\n";
		$data[] = ' ';

		$csv = 'en-attribute-test.csv';
		$filename = 'attribute-import.zip';

		$fh = fopen( $csv, 'w' );

		foreach( $data as $id => $row ) {
			fwrite( $fh, $row );
		}

		fclose( $fh );


		$zip = new ZipArchive();
		$zip->open($filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		$zip->addFile($csv, $csv);
		$zip->close();

		if( unlink( $csv ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $filename;

		$this->_object->importFile( $params );

		$textManager = MShop_Text_Manager_Factory::createManager( $this->_context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'attribute' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'unittest:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = array();
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );
		$listManager = $attributeManager->getSubManager( 'list' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'attribute.list.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'attribute.list.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}

		foreach( $textItems as $item ) {
			$this->assertEquals( 'unittest:', substr( $item->getContent(), 0, 9 ) );
		}

		$this->assertEquals( 6, count( $textItems ) );
		$this->assertEquals( 6, count( $listItems ) );

		if( file_exists( $filename ) !== false ) {
			throw new Exception( 'Import file was not removed' );
		}
	}


	public function testImportFromXLSFile()
	{
		$this->_context = TestHelper::getContext();
		$this->_context->getConfig()->set( 'controller/extjs/product/export/text/default/container', '.xls' );
		$this->_context->getConfig()->set( 'controller/extjs/product/export/text/default/contentReader', 'Excel5' );
		$this->_context->getConfig()->set( 'controller/extjs/product/export/text/default/contentExtension', '' );
		$this->_object = new Controller_ExtJS_Attribute_Import_Text_Default( $this->_context );

		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

		$ids = array();
		foreach( $attributeManager->searchItems( $attributeManager->createSearch() ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'en' );
		$params->items = $ids;
		$params->site = $this->_context->getLocale()->getSite()->getCode();

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$exporter = new Controller_ExtJS_Attribute_Export_Text_Default( $this->_context );
		$result = $exporter->exportFile( $params );

		$this->assertTrue( array_key_exists('file', $result) );

		$filename = substr($result['file'], 9, -14);
		$this->assertTrue( file_exists( $filename ) );

		$filename2 = 'attribute-import.xls';

		$phpExcel = PHPExcel_IOFactory::load($filename);

		if( unlink( $filename ) !== true ) {
			throw new Exception( sprintf( 'Deleting file "%1$s" failed', $filename ) );
		}

		$sheet = $phpExcel->getSheet( 0 );

		$sheet->setCellValueByColumnAndRow( 6, 2, 'Root: delivery info' );
		$sheet->setCellValueByColumnAndRow( 6, 3, 'Root: long' );
		$sheet->setCellValueByColumnAndRow( 6, 4, 'Root: name' );
		$sheet->setCellValueByColumnAndRow( 6, 5, 'Root: payment info' );
		$sheet->setCellValueByColumnAndRow( 6, 6, 'Root: short' );

		$objWriter = PHPExcel_IOFactory::createWriter( $phpExcel, 'Excel5' );
		$objWriter->save( $filename2 );

		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $filename2;

		$this->_object->importFile( $params );

		if( file_exists( $filename2 ) !== false ) {
			throw new Exception( 'Import file was not removed' );
		}

		$textManager = MShop_Text_Manager_Factory::createManager( $this->_context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'Root:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = array();
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$listManager = $attributeManager->getSubManager( 'list' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'attribute.list.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'attribute.list.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}


		$this->assertEquals( 5, count( $textItems ) );
		$this->assertEquals( 5, count( $listItems ) );

		foreach( $textItems as $item ) {
			$this->assertEquals( 'Root:', substr( $item->getContent(), 0, 5 ) );
		}
	}


	public function testUploadFile()
	{
		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->_context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt',
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';
		$params->site = $this->_context->getLocale()->getSite()->getCode();

		$result = $this->_object->uploadFile( $params );

		$this->assertTrue( file_exists( $result['items'] ) );
		unlink( $result['items'] );

		$params = (object) array(
			'site' => 'unittest',
			'condition' => (object) array( '&&' => array( 0 => (object) array( '~=' => (object) array( 'job.label' => 'file.txt' ) ) ) ),
		);

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 1, count( $result['items'] ) );

		$deleteParams = (object) array(
			'site' => 'unittest',
			'items' => $result['items'][0]->{'job.id'},
		);

		$jobController->deleteItems( $deleteParams );

		$result = $jobController->searchItems( $params );
		$this->assertEquals( 0, count( $result['items'] ) );
	}


	public function testUploadFileExeptionNoFiles()
	{
		$params = new stdClass();
		$params->items = basename( $this->_testfile );
		$params->site = 'unittest';

		$_FILES = array();

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$result = $this->_object->uploadFile( $params );
	}


	public function testUploadFileExeptionNotAFileUpload()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_OK,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionSize()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_FORM_SIZE,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionPartial()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_PARTIAL,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionNoFile()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_NO_FILE,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionNoTmpDir()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_NO_TMP_DIR,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionWriteError()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_CANT_WRITE,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionExtError()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => UPLOAD_ERR_EXTENSION,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testAbstractCheckFileUploadExceptionOtherError()
	{
		$res = $this->_prepareCheckFileUpload();
		$params = $res[0];
		$object = $res[1];

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => 9,
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );
	}


	public function testUploadFileExceptionWrongDestination()
	{
		set_error_handler( 'TestHelper::errorHandler' );

		$this->_context->getConfig()->set('controller/extjs/attribute/import/text/default/uploaddir', '/up/');
		$this->_context->getConfig()->set('controller/extjs/attribute/import/text/default/enablecheck', false);

		$object = new Controller_ExtJS_Attribute_Import_Text_Default( $this->_context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$params = new stdClass();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';
		$params->site = $this->_context->getLocale()->getSite()->getCode();

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => basename( $this->_testfile ),
			'error' => 'anError',
		);

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$object->uploadFile( $params );

		restore_error_handler();
	}


	public function testAbstractGetItemSchema()
	{
		$actual = $this->_object->getItemSchema();
		$expected = array(
			'name' => 'Attribute_Import_Text',
			'properties' => array(),
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractGetSearchSchema()
	{
		$actual = $this->_object->getSearchSchema();
		$expected = array(
			'criteria' => array()
		);

		$this->assertEquals( $expected, $actual );
	}


	public function testAbstractSetLocaleException()
	{
		$params = (object) array(
			'site' => 'badSite',
			'items' => (object) array(),
		);
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->uploadFile( $params );
	}


	public function testAbstractCheckParamsException()
	{
		$params = (object) array();
		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->uploadFile( $params );
	}


	protected function _prepareCheckFileUpload()
	{
		$this->_context->getConfig()->set('controller/extjs/attribute/import/text/default/enablecheck', true);
		$object = new Controller_ExtJS_Attribute_Import_Text_Default( $this->_context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		$directory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testdir';

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$params = new stdClass();
		$params->items = $this->_testfile;
		$params->site = $this->_context->getLocale()->getSite()->getCode();

		return array($params,$object);
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Attribute_Import_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_testdir;
	private $_testfile;


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
		$context = TestHelper::getContext();

		$this->_testdir = $context->getConfig()->get( 'controller/extjs/attribute/import/text/default/uploaddir', './tmp' );
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Attribute_Import_Text_Default( $context );
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


	public function testImportFile()
	{
		$context = TestHelper::getContext();
		$attributeManager = MShop_Attribute_Manager_Factory::createManager( $context );

		$ids = array();
		foreach( $attributeManager->searchItems( $attributeManager->createSearch() ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'de', 'en' );
		$params->items = $ids;
		$params->site = $context->getLocale()->getSite()->getCode();

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$exporter = new Controller_ExtJS_Attribute_Export_Text_Default( $context );
		$exporter->createHttpOutput( $params );

		$content = ob_get_contents();
		ob_end_clean();


		$filename = 'attribute-import.xlsx';

		if( file_put_contents( $filename, $content ) === false ) {
			throw new Exception( 'Unable write import file' );
		}

		$phpExcel = PHPExcel_IOFactory::load( $filename );

		$phpExcel->setActiveSheetIndex( 1 );
		$sheet = $phpExcel->getActiveSheet();

		$sheet->setCellValueByColumnAndRow( 6, 2, 'white: img-desc' );
		$sheet->setCellValueByColumnAndRow( 6, 3, 'white: long' );
		$sheet->setCellValueByColumnAndRow( 6, 4, 'white: name' );
		$sheet->setCellValueByColumnAndRow( 6, 5, 'white: short' );
		// test exceptions for text types
		$sheet->setCellValueByColumnAndRow( 4, 2, 'bad-text-type-exception1' );
		$sheet->setCellValueByColumnAndRow( 5, 2, 'bad-text-type-id-exception2' );
		// test exceptions for list types
		$sheet->setCellValueByColumnAndRow( 3, 3, 'bad-list-type-exception3' );

		$objWriter = PHPExcel_IOFactory::createWriter( $phpExcel, 'Excel2007' );
		$objWriter->save( $filename );


		$params = new stdClass();
		$params->site = $context->getLocale()->getSite()->getCode();
		$params->items = $filename;

		$this->_object->importFile( $params );


		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'attribute' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'white:' );
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


		$this->assertEquals( 3, count( $textItems ) ); // 4 without exception testing
		$this->assertEquals( 2, count( $listItems ) ); // 4 without exception testing

		foreach( $textItems as $item ) {
			$this->assertEquals( 'white:', substr( $item->getContent(), 0, 6 ) );
		}

		if( file_exists( $filename ) !== false ) {
			throw new Exception( 'Import file was not removed' );
		}
	}


	public function testUploadFile()
	{
		$context = TestHelper::getContext();
		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt',
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';
		$params->site = $context->getLocale()->getSite()->getCode();

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

		$ctx = TestHelper::getContext();
		$ctx->getConfig()->set('controller/extjs/attribute/import/text/default/uploaddir', '/up/');
		$ctx->getConfig()->set('controller/extjs/attribute/import/text/default/enablecheck', false);

		$object = new Controller_ExtJS_Attribute_Import_Text_Default( $ctx );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$params = new stdClass();
		$params->items = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';
		$params->site = $ctx->getLocale()->getSite()->getCode();

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
		$ctx = TestHelper::getContext();
		$ctx->getConfig()->set('controller/extjs/attribute/import/text/default/enablecheck', true);
		$object = new Controller_ExtJS_Attribute_Import_Text_Default( $ctx );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		$directory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testdir';

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );

		$params = new stdClass();
		$params->items = $this->_testfile;
		$params->site = TestHelper::getContext()->getLocale()->getSite()->getCode();

		return array($params,$object);
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_Import_Text_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_Import_Text_DefaultTest' );
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

		$this->_testdir = $this->_context->getConfig()->get( 'controller/extjs/product/import/text/default/uploaddir', './tmp' );
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Product_Import_Text_Default( $this->_context );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
	}


	public function testGetServiceDescription()
	{
		$desc = $this->_object->getServiceDescription();
		$this->assertInternalType( 'array', $desc );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Product_Import_Text.importFile'] ) );
	}


	public function testImportFromCSVFile()
	{
		$data[] = '"Language ID","Type","Code","List type","Text type","Text ID","Text"'."\n";
		$data[] = '"en","product","ABCD","default","long","","ABCD: long"'."\n";
		$data[] = '"en","product","ABCD","default","metadescription","","ABCD: meta desc"' ."\n";
		$data[] = '"en","product","ABCD","default","metakeywords","","ABCD: meta keywords"'."\n";
		$data[] = '"en","product","ABCD","default","metatitle","","ABCD: meta title"'."\n";
		$data[] = '"en","product","ABCD","default","name","","ABCD: name"'."\n";
		$data[] = '"en","product","ABCD","default","short","","ABCD: short"'."\n";
		$data[] = ' ';

		$ds = DIRECTORY_SEPARATOR;
		$csv = 'en-product-test.csv';
		$filename = PATH_TESTS . $ds . 'tmp' . $ds . 'product-import.zip';

		if( file_put_contents( PATH_TESTS . $ds . 'tmp' . $ds . $csv, implode( '', $data ) ) === false ) {
			throw new Exception( sprintf( 'Unable to write test file "%1$s"', $csv ) );
		}

		$zip = new ZipArchive();
		$zip->open( $filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
		$zip->addFile( PATH_TESTS . $ds . 'tmp' . $ds . $csv, $csv );
		$zip->close();

		if( unlink( PATH_TESTS . $ds . 'tmp' . $ds . $csv ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}


		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $filename;

		$this->_object->importFile( $params );

		$textManager = MShop_Text_Manager_Factory::createManager( $this->_context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'product' );
		$expr[] = $criteria->compare( '==', 'text.languageid', 'en' );
		$expr[] = $criteria->compare( '==', 'text.status', 1 );
		$expr[] = $criteria->compare( '~=', 'text.content', 'ABCD:' );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$textItems = $textManager->searchItems( $criteria );

		$textIds = array();
		foreach( $textItems as $item )
		{
			$textManager->deleteItem( $item->getId() );
			$textIds[] = $item->getId();
		}


		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$listManager = $productManager->getSubManager( 'list' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.list.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'product.list.refid', $textIds );
		$criteria->setConditions( $criteria->combine( '&&', $expr ) );

		$listItems = $listManager->searchItems( $criteria );

		foreach( $listItems as $item ) {
			$listManager->deleteItem( $item->getId() );
		}


		foreach( $textItems as $item ) {
			$this->assertEquals( 'ABCD:', substr( $item->getContent(), 0, 5 ) );
		}

		$this->assertEquals( 6, count( $textItems ) );
		$this->assertEquals( 6, count( $listItems ) );

		if( file_exists( $filename ) !== false ) {
			throw new Exception( 'Import file was not removed' );
		}
	}


	public function testUploadFile()
	{
		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $this->_context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );


		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->_testfile,
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testfile;
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


	public function testUploadFileExceptionNoUploadFile()
	{
		$_FILES = array();

		$params = new stdClass();
		$params->items = 'file.txt';
		$params->site = $this->_context->getLocale()->getSite()->getCode();

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$this->_object->uploadFile( $params );
	}
}
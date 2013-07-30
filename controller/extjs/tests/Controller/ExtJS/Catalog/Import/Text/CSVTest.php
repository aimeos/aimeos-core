<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Catalog_Import_Text_CSVTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Catalog_Import_Text_CSVTest' );
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
		$this->_testdir = $context->getConfig()->get( 'controller/extjs/catalog/import/text/default/uploaddir', './tmp' );
		$this->_testfile = $this->_testdir . DIRECTORY_SEPARATOR . 'file.txt';

		if( !is_dir( $this->_testdir ) && mkdir( $this->_testdir, 0775, true ) === false ) {
			throw new Exception( sprintf( 'Unable to create missing upload directory "%1$s"', $this->_testdir ) );
		}

		$this->_object = new Controller_ExtJS_Catalog_Import_Text_CSV( $context );
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
		$this->assertEquals( 2, count( $desc['Catalog_Import_Text.uploadFile'] ) );
		$this->assertEquals( 2, count( $desc['Catalog_Import_Text.importFile'] ) );
	}

	public function testImportFile()
	{
		$context = TestHelper::getContext();
		$catalogManager = MShop_Catalog_Manager_Factory::createManager( $context );

		$node = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$params = new stdClass();
		$params->lang = array( 'en' );
		$params->items = $node->getId();
		$params->site = $context->getLocale()->getSite()->getCode();

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$exporter = new Controller_ExtJS_Catalog_Export_Text_CSV( $context );
		$result = $exporter->exportFile( $params );

		$file = substr( $result['file'], 9, -14);
		$this->assertTrue( file_exists( $file ) );

		$zip = new ZipArchive();
		$zip->open($file);

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'catalogcsvexport';
		if( mkdir( $testdir ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
		}

		$zip->extractTo( $testdir );
		$zip->close();

		if( unlink( $file ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$enCSV = $testdir . DIRECTORY_SEPARATOR . 'en.csv';

		$this->assertTrue( file_exists( $enCSV ) );
		$fh = fopen( $enCSV, 'r' );
		while( ( $data = fgetcsv( $fh ) ) != false ) {
			$lines[] = $data;
		}
		fclose( $fh );

		$lines[1][6] = 'Root: delivery info';
		$lines[2][6] = 'Root: long';
		$lines[3][6] = 'Root: name';
		$lines[4][6] = 'Root: payment info';
		$lines[5][6] = 'Root: short';

		$fh = fopen( $enCSV, 'w' );
		for( $i = 1; $i<6; $i++) {
			fputcsv( $fh, $lines[$i] );
		}
		fclose( $fh );

		$params = new stdClass();
		$params->site = $context->getLocale()->getSite()->getCode();
		$params->items = $enCSV;

		$this->_object->importFile( $params );

		if( rmdir( 'tmp' . DIRECTORY_SEPARATOR . 'catalogcsvexport' ) !== true ) {
			throw new Exception( sprintf( 'Deleting dir failed' ) );
		}

		$textManager = MShop_Text_Manager_Factory::createManager( $context );
		$criteria = $textManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'text.domain', 'catalog' );
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


		$listManager = $catalogManager->getSubManager( 'list' );
		$criteria = $listManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'catalog.list.domain', 'text' );
		$expr[] = $criteria->compare( '==', 'catalog.list.refid', $textIds );
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
		$context = TestHelper::getContext();
		$jobController = Controller_ExtJS_Admin_Job_Factory::createController( $context );

		$testfiledir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfiles' . DIRECTORY_SEPARATOR;
		$directory = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testdir';

		exec( sprintf( 'cp -r %1$s %2$s', escapeshellarg( $testfiledir ) . '*', escapeshellarg( $this->_testdir ) ) );


		$_FILES['unittest'] = array(
			'name' => 'file.txt',
			'tmp_name' => $this->_testfile,
			'error' => UPLOAD_ERR_OK,
		);

		$params = new stdClass();
		$params->items = $this->_testfile;
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
		$params->items = 'test.txt';
		$params->site = 'unittest';

		$_FILES = array();

		$this->setExpectedException( 'Controller_ExtJS_Exception' );
		$result = $this->_object->uploadFile( $params );
	}

}
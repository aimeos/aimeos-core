<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Product_Export_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Product_Export_Text_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Product_Export_Text_Default( $this->_context );
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


	public function testExportCSVFile()
	{
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$criteria = $productManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.code', 'CNE' );
		$criteria->setConditions( $criteria->compare( '==', 'product.code', 'CNE' ) );

		$searchResult = $productManager->searchItems( $criteria );

		if ( ( $productItem = reset( $searchResult ) ) === false ) {
			throw new Exception( 'No item with product code CNE found' );
		}

		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $productItem->getId();
		$params->lang = 'de';

		$result = $this->_object->exportFile( $params );
		$file = substr( $result['file'], 9, -14 );

		$this->assertTrue( file_exists( $file ) );

		$zip = new ZipArchive();
		$zip->open($file);

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
		if( mkdir( $testdir ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
		}

		$zip->extractTo( $testdir );
		$zip->close();

		if( unlink( $file ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$deCSV = $testdir . DIRECTORY_SEPARATOR . 'de.csv';

		$this->assertTrue( file_exists( $deCSV ) );
		$fh = fopen( $deCSV, 'r' );
		while( ( $data = fgetcsv( $fh ) ) != false ) {
			$lines[] = $data;
		}
		fclose( $fh );
		if( unlink( $deCSV ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		if( rmdir( $testdir ) === false ) {
			throw new Exception( 'Unable to remove test export directory' );
		}

		$this->assertEquals( $lines[0][0], 'Language ID' );
		$this->assertEquals( $lines[0][1], 'Product type' );
		$this->assertEquals( $lines[0][2], 'Product code' );
		$this->assertEquals( $lines[0][3], 'List type' );
		$this->assertEquals( $lines[0][4], 'Text type' );
		$this->assertEquals( $lines[0][5], 'Text ID' );
		$this->assertEquals( $lines[0][6], 'Text' );

		$this->assertEquals( 'de', $lines[2][0] );
		$this->assertEquals( 'default', $lines[2][1] );
		$this->assertEquals( 'CNE', $lines[2][2] );
		$this->assertEquals( 'unittype13', $lines[2][3] );
		$this->assertEquals( 'metadescription', $lines[2][4] );
		$this->assertEquals( 'Expresso', $lines[2][6] );
	}


	public function testExportXLSFile()
	{
		$this->_context = TestHelper::getContext();
		$this->_context->getConfig()->set( 'controller/extjs/product/export/text/default/container', 'xls' );
		$this->_context->getConfig()->set( 'controller/extjs/product/export/text/default/contentExtension', '' );
		$this->_object = new Controller_ExtJS_Product_Export_Text_Default( $this->_context );

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$criteria = $productManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.code', 'CNE' );
		$criteria->setConditions( $criteria->compare( '==', 'product.code', 'CNE' ) );

		$searchResult = $productManager->searchItems( $criteria );

		if ( ( $productItem = reset( $searchResult ) ) === false ) {
			throw new Exception( 'No item with product code CNE found' );
		}

		$params = new stdClass();
		$params->site = $this->_context->getLocale()->getSite()->getCode();
		$params->items = $productItem->getId();
		$params->lang = 'de';

		$result = $this->_object->exportFile( $params );

		$this->assertTrue( array_key_exists('file', $result) );

		$file = substr($result['file'], 9, -14);

		$this->assertTrue( file_exists( $file ) );

		$inputFileType = PHPExcel_IOFactory::identify( $file );
		$objReader = PHPExcel_IOFactory::createReader( $inputFileType );
		$objReader->setLoadSheetsOnly( $params->lang );
		$objPHPExcel = $objReader->load( $file );

		if( unlink( $file ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$objWorksheet = $objPHPExcel->getActiveSheet();

		$product = $productItem->toArray();

		for ( $i = 2; $i < 8; $i++ )
		{
			$this->assertEquals( $params->lang, $objWorksheet->getCellByColumnAndRow( 0, $i )->getValue() );
			$this->assertEquals( $product['product.type'], $objWorksheet->getCellByColumnAndRow( 1, $i )->getValue() );
			$this->assertEquals( $product['product.code'], $objWorksheet->getCellByColumnAndRow( 2, $i )->getValue() );
		}

		$this->assertEquals( 'List type', $objWorksheet->getCellByColumnAndRow( 3, 1 )->getValue() );
		$this->assertEquals( 'Text type', $objWorksheet->getCellByColumnAndRow( 4, 1 )->getValue() );
		$this->assertEquals( 'Text ID', $objWorksheet->getCellByColumnAndRow( 5, 1 )->getValue() );
		$this->assertEquals( 'Text', $objWorksheet->getCellByColumnAndRow( 6, 1 )->getValue() );
	}


	public function testGetServiceDescription()
	{
		$expected = array(
			'Product_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "array","name" => "lang","optional" => true ),
				),
				"returns" => "",
			),
		);

		$actual = $this->_object->getServiceDescription();

		$this->assertEquals( $expected, $actual );
	}
}
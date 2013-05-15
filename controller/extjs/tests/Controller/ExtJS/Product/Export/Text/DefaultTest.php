<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14411 2011-12-17 14:02:37Z nsendetzky $
 */


class Controller_ExtJS_Product_Export_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;


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
		$this->_object = new Controller_ExtJS_Product_Export_Text_Default( TestHelper::getContext() );
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


	public function testcreateHttpOutput()
	{
		$context = TestHelper::getContext();
		$manager = MShop_Product_Manager_Factory::createManager( $context );
		$textTypeManager = MShop_Text_Manager_Factory::createManager( $context )->getSubManager('type');


		$typeTotal = 0;
		$typeSearch = $textTypeManager->createSearch();
		$typeSearch->setConditions( $typeSearch->compare( '==', 'text.type.domain', 'product' ) );
		$typeSearch->setSlice( 0, 0 );
		$textTypeManager->searchItems( $typeSearch, array(), $typeTotal );


		$ids = array();
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '!=', 'product.code', array( 'U:HIS', 'U:HISSUB01', 'U:HISSUB02' ) ) );
		foreach( $manager->searchItems( $search ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'de', 'en' );
		$params->items = $ids;
		$params->site = 'unittest';

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$this->_object->createHttpOutput( $params );

		$content = ob_get_contents();
		ob_end_clean();

		$filename = 'product-export.xls';

		if( file_put_contents( $filename, $content ) === false ) {
			throw new Exception( 'Unable to write export file' );
		}

		$phpExcel = PHPExcel_IOFactory::load($filename);

		if( unlink( $filename ) === false ) {
			throw new exception( 'unable to remove export file' );
		}


		$phpExcel->setActiveSheetIndex(0);
		$sheet = $phpExcel->getActiveSheet();

		$this->assertEquals( 'Language ID', $sheet->getCell('A1')->getValue() );
		$this->assertEquals( 'Text', $sheet->getCell('G1')->getValue() );

		for( $i = 2; $i < $typeTotal + 1; $i++ )
		{
			if( $sheet->getCell( 'E' . $i )->getValue() == 'name' ) {
				break;
			}
		}
		$this->assertLessThan( $typeTotal, $i );

		$this->assertEquals( 'de', $sheet->getCell('A' . $i)->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('B' . $i)->getValue() );
		$this->assertEquals( 'ABCD', $sheet->getCell('C' . $i)->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('D' . $i)->getValue() );
		$this->assertEquals( 'name', $sheet->getCell('E' . $i)->getValue() );
		$this->assertEquals( 'Unterproduct 1', $sheet->getCell('G' . $i)->getValue() );
	}


	public function testExportFile()
	{
		$context = TestHelper::getContext();

		$productManager = MShop_Product_Manager_Factory::createManager( $context );
		$criteria = $productManager->createSearch();

		$expr = array();
		$expr[] = $criteria->compare( '==', 'product.code', 'CNE' );
		$criteria->setConditions( $criteria->compare( '==', 'product.code', 'CNE' ) );

		$searchResult = $productManager->searchItems( $criteria );

		if ( ( $productItem = reset( $searchResult ) ) === false ) {
			throw new Exception( 'No item with product code CNE found' );
		}


		$params = new stdClass();
		$params->site = $context->getLocale()->getSite()->getCode();
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

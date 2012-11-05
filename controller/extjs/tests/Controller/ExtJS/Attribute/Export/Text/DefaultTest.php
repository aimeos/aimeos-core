<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @version $Id: DefaultTest.php 14602 2011-12-27 15:27:08Z gwussow $
 */


class Controller_ExtJS_Attribute_Export_Text_DefaultTest extends MW_Unittest_Testcase
{
	protected $_object;


	/**
	 * Runs the test methods of this class.
	 *
	 * @access public
	 * @static
	 */
	public static function main()
	{
		require_once 'PHPUnit/TextUI/TestRunner.php';

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Attribute_Export_Text_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Attribute_Export_Text_Default( TestHelper::getContext() );
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
		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$ids = array();
		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'de' );
		$params->items = $ids;
		$params->site = 'unittest';

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$this->_object->createHttpOutput( $params );

		$content = ob_get_contents();
		ob_end_clean();


		$filename = 'attribute-export.xls';

		if( file_put_contents( $filename, $content ) === false ) {
			throw new Exception( 'Unable to write export file' );
		}

		$phpExcel = PHPExcel_IOFactory::load($filename);

		if( unlink( $filename ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}


		$phpExcel->setActiveSheetIndex(0);
		$sheet = $phpExcel->getActiveSheet();

		$this->assertEquals( 'Language ID', $sheet->getCell('A1')->getValue() );
		$this->assertEquals( 'Text', $sheet->getCell('G1')->getValue() );

		$this->assertEquals( 'de', $sheet->getCell('A8')->getValue() );
		$this->assertEquals( 'color', $sheet->getCell('B8')->getValue() );
		$this->assertEquals( 'white', $sheet->getCell('C8')->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('D8')->getValue() );
		$this->assertEquals( 'name', $sheet->getCell('E8')->getValue() );
		$this->assertEquals( 'weiß', $sheet->getCell('G8')->getValue() );


		$this->assertEquals( '', $sheet->getCell('A124')->getValue() );
		$this->assertEquals( 'width', $sheet->getCell('B124')->getValue() );
		$this->assertEquals( '36', $sheet->getCell('C124')->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('D124')->getValue() );
		$this->assertEquals( 'name', $sheet->getCell('E124')->getValue() );
		$this->assertEquals( '36', $sheet->getCell('G124')->getValue() );
	}


	public function testExportFile()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

		$ids = array();
		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
			$ids[] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'de' );
		$params->items = $ids;
		$params->site = 'unittest';

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

		$sheet = $objPHPExcel->getActiveSheet();

		$this->assertEquals( 'Language ID', $sheet->getCell('A1')->getValue() );
		$this->assertEquals( 'Text', $sheet->getCell('G1')->getValue() );

		$this->assertEquals( 'de', $sheet->getCell('A8')->getValue() );
		$this->assertEquals( 'color', $sheet->getCell('B8')->getValue() );
		$this->assertEquals( 'white', $sheet->getCell('C8')->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('D8')->getValue() );
		$this->assertEquals( 'name', $sheet->getCell('E8')->getValue() );
		$this->assertEquals( 'weiß', $sheet->getCell('G8')->getValue() );


		$this->assertEquals( '', $sheet->getCell('A124')->getValue() );
		$this->assertEquals( 'width', $sheet->getCell('B124')->getValue() );
		$this->assertEquals( '36', $sheet->getCell('C124')->getValue() );
		$this->assertEquals( 'default', $sheet->getCell('D124')->getValue() );
		$this->assertEquals( 'name', $sheet->getCell('E124')->getValue() );
		$this->assertEquals( '36', $sheet->getCell('G124')->getValue() );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->_object->getServiceDescription();
		$expected = array(
			'Attribute_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string", "name" => "site", "optional" => false ),
					array( "type" => "array", "name" => "items", "optional" => false ),
					array( "type" => "array", "name" => "lang", "optional" => true ),
				),
				"returns" => "",
			),
		);

		$this->assertEquals( $expected, $actual );
	}

}

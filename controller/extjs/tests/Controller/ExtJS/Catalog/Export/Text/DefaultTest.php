<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Catalog_Export_Text_DefaultTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Catalog_Export_Text_DefaultTest' );
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
		$this->_object = new Controller_ExtJS_Catalog_Export_Text_Default( TestHelper::getContext() );
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
		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $manager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.label', array( 'Root', 'Tee' ) ) );

		$ids = array();
		foreach ( $manager->searchItems( $search ) as $item ) {
			$ids[$item->getLabel()] = $item->getId();
		}


		$params = new stdClass();
		$params->lang = array( 'de', 'fr' );
		$params->items = array( $node->getId() );
		$params->site = 'unittest';

		if( ob_start() === false ) {
			throw new Exception( 'Unable to start output buffering' );
		}

		$this->_object->createHttpOutput( $params );

		$content = ob_get_contents();
		ob_end_clean();


		$filename = 'catalog-export.xls';

		if( file_put_contents( $filename, $content ) === false ) {
			throw new Exception( 'Unable to write export file' );
		}

		$phpExcel = PHPExcel_IOFactory::load($filename);

		if( unlink( $filename ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$phpExcel->setActiveSheetIndex( 0 );
		$sheet = $phpExcel->getActiveSheet();

		$this->assertEquals( 'Language ID', $sheet->getCell( 'A1' )->getValue() );
		$this->assertEquals( 'Text', $sheet->getCell( 'G1' )->getValue() );

		$this->assertEquals( 'de', $sheet->getCell( 'A4' )->getValue() );
		$this->assertEquals( 'Root', $sheet->getCell( 'B4' )->getValue() );
		$this->assertEquals( $ids['Root'], $sheet->getCell( 'C4' )->getValue() );
		$this->assertEquals( 'default', $sheet->getCell( 'D4' )->getValue() );
		$this->assertEquals( 'name', $sheet->getCell( 'E4' )->getValue() );
		$this->assertEquals( '', $sheet->getCell( 'G4' )->getValue() );

		$this->assertEquals( 'de', $sheet->getCell( 'A21' )->getValue() );
		$this->assertEquals( 'Tee', $sheet->getCell( 'B21' )->getValue() );
		$this->assertEquals( $ids['Tee'], $sheet->getCell( 'C21' )->getValue() );
		$this->assertEquals( 'unittype8', $sheet->getCell( 'D21' )->getValue() );
		$this->assertEquals( 'long', $sheet->getCell( 'E21' )->getValue() );
		$this->assertEquals( 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.', $sheet->getCell( 'G21' )->getValue() );
	}

	public function testExportFile()
	{
		$context = TestHelper::getContext();

		$manager = MShop_Catalog_Manager_Factory::createManager( TestHelper::getContext() );
		$node = $manager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'catalog.label', array( 'Root', 'Tee' ) ) );

		$ids = array();
		foreach ( $manager->searchItems( $search ) as $item ) {
			$ids[$item->getLabel()] = $item->getId();
		}

		$params = new stdClass();
		$params->lang = array( 'de', 'fr' );
		$params->items = array( $node->getId() );
		$params->site = $context->getLocale()->getSite()->getCode();

		$result = $this->_object->exportFile( $params );

		$this->assertTrue( array_key_exists('file', $result) );

		$file = substr($result['file'], 9, -14);
		$this->assertTrue( file_exists( $file ) );


		$inputFileType = PHPExcel_IOFactory::identify( $file );
		$objReader = PHPExcel_IOFactory::createReader( $inputFileType );
		$objPHPExcel = $objReader->load( $file );
		$objPHPExcel->setActiveSheetIndex( 0 );

		if( unlink( $file ) === false ) {
			throw new Exception( 'Unable to remove export file' );
		}

		$sheet = $objPHPExcel->getActiveSheet();

		$this->assertEquals( 'Language ID', $sheet->getCell( 'A1' )->getValue() );
		$this->assertEquals( 'Text', $sheet->getCell( 'G1' )->getValue() );

		$this->assertEquals( 'de', $sheet->getCell( 'A4' )->getValue() );
		$this->assertEquals( 'Root', $sheet->getCell( 'B4' )->getValue() );
		$this->assertEquals( $ids['Root'], $sheet->getCell( 'C4' )->getValue() );
		$this->assertEquals( 'default', $sheet->getCell( 'D4' )->getValue() );
		$this->assertEquals( 'name', $sheet->getCell( 'E4' )->getValue() );
		$this->assertEquals( '', $sheet->getCell( 'G4' )->getValue() );

		$this->assertEquals( 'de', $sheet->getCell( 'A21' )->getValue() );
		$this->assertEquals( 'Tee', $sheet->getCell( 'B21' )->getValue() );
		$this->assertEquals( $ids['Tee'], $sheet->getCell( 'C21' )->getValue() );
		$this->assertEquals( 'unittype8', $sheet->getCell( 'D21' )->getValue() );
		$this->assertEquals( 'long', $sheet->getCell( 'E21' )->getValue() );
		$this->assertEquals( 'Dies würde die lange Beschreibung der Teekategorie sein. Auch hier machen Bilder einen Sinn.', $sheet->getCell( 'G21' )->getValue() );
	}


	public function testGetServiceDescription()
	{
		$actual = $this->_object->getServiceDescription();
		$expected = array(
			'Catalog_Export_Text.createHttpOutput' => array(
				"parameters" => array(
					array( "type" => "string","name" => "site","optional" => false ),
					array( "type" => "array","name" => "items","optional" => false ),
					array( "type" => "array","name" => "lang","optional" => true ),
				),
				"returns" => "",
			),
		);

		$this->assertEquals( $expected, $actual );
	}

}

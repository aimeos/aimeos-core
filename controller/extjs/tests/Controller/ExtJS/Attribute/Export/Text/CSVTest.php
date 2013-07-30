<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Attribute_Export_Text_CSVTest extends MW_Unittest_Testcase
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

		$suite  = new PHPUnit_Framework_TestSuite( 'Controller_ExtJS_Attribute_Export_Text_CSVTest' );
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
		$this->_object = new Controller_ExtJS_Attribute_Export_Text_CSV( TestHelper::getContext() );
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


	public function testExportFile()
	{
// 		$context = TestHelper::getContext();

// 		$manager = MShop_Attribute_Manager_Factory::createManager( TestHelper::getContext() );

// 		$ids = array();
// 		foreach( $manager->searchItems( $manager->createSearch() ) as $item ) {
// 			$ids[] = $item->getId();
// 		}

// 		$params = new stdClass();
// 		$params->lang = array( 'de' );
// 		$params->items = $ids;
// 		$params->site = 'unittest';

// 		$result = $this->_object->exportFile( $params );

// 		$this->assertTrue( array_key_exists('file', $result) );

// 		$file = substr($result['file'], 9, -14);
// 		$this->assertTrue( file_exists( $file ) );


// 		$zip = new ZipArchive();
// 		$zip->open($file);

// 		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
// 		if( mkdir( $testdir ) === false ) {
// 			throw new Controller_ExtJS_Exception( sprintf( 'Couldn\'t create directory "csvexport"' ) );
// 		}

// 		$zip->extractTo( $testdir );
// 		$zip->close();

// 		if( unlink( $file ) === false ) {
// 			throw new Exception( 'Unable to remove export file' );
// 		}

// 		$deCSV = $testdir . DIRECTORY_SEPARATOR . 'de.csv';

// 		$this->assertTrue( file_exists( $deCSV ) );
// 		$fh = fopen( $deCSV, 'r' );
// 		while( ( $data = fgetcsv( $fh ) ) != false ) {
// 			$lines[] = $data;
// 		}

// 		fclose( $fh );
// 		if( unlink( $deCSV ) === false ) {
// 			throw new Exception( 'Unable to remove export file' );
// 		}

// 		if( rmdir( $testdir ) === false ) {
// 			throw new Exception( 'Unable to remove test export directory' );
// 		}


// 		$this->assertEquals( 'Language ID', $lines[0][0] );
// 		$this->assertEquals( 'Text', $lines[0][6] );

// 		$this->assertEquals( 'de', $lines[7][0] );
// 		$this->assertEquals( 'color', $lines[7][1] );
// 		$this->assertEquals( 'white', $lines[7][2] );
// 		$this->assertEquals( 'default', $lines[7][3] );
// 		$this->assertEquals( 'name', $lines[7][4] );
// 		$this->assertEquals( 'weiß', $lines[7][6] );


// 		$this->assertEquals( '', $lines[123][0] );
// 		$this->assertEquals( 'width', $lines[123][1] );
// 		$this->assertEquals( '36', $lines[123][2] );
// 		$this->assertEquals( 'default', $lines[123][3] );
// 		$this->assertEquals( 'name', $lines[123][4] );
// 		$this->assertEquals( '36', $lines[123][6] );
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

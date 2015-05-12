<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_ExtJS_Attribute_Export_Text_DefaultTest extends MW_Unittest_Testcase
{
	private $_object;
	private $_context;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->_context = TestHelper::getContext();
		$this->_object = new Controller_ExtJS_Attribute_Export_Text_Default( $this->_context );
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
		$manager = MShop_Attribute_Manager_Factory::createManager( $this->_context );

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


		$zip = new ZipArchive();
		$zip->open($file);

		$testdir = 'tmp' . DIRECTORY_SEPARATOR . 'csvexport';
		if( !is_dir( $testdir ) && mkdir( $testdir, 0755, true ) === false ) {
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
		$lines = array();

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

		$this->assertEquals( 'Language ID', $lines[0][0] );
		$this->assertEquals( 'Text', $lines[0][6] );

		$this->assertEquals( 'de', $lines[8][0] );
		$this->assertEquals( 'color', $lines[8][1] );
		$this->assertEquals( 'red', $lines[8][2] );
		$this->assertEquals( 'default', $lines[8][3] );
		$this->assertEquals( 'name', $lines[8][4] );
		$this->assertEquals( '', $lines[8][6] );

		$this->assertEquals( '', $lines[158][0] );
		$this->assertEquals( 'width', $lines[158][1] );
		$this->assertEquals( '29', $lines[158][2] );
		$this->assertEquals( 'default', $lines[158][3] );
		$this->assertEquals( 'name', $lines[158][4] );
		$this->assertEquals( '29', $lines[158][6] );
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

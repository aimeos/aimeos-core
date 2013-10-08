<?php

/**
 * Test class for MW_Container_Content_CSV.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Container_Content_CSVTest extends MW_Unittest_Testcase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}


	public function testNewFile()
	{
		$csv = new MW_Container_Content_CSV( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile.csv', 'temp.txt' );

		$check = file_exists( $csv->getResource() );
		unlink( $csv->getResource() );

		$this->assertEquals( true, $check );
		$this->assertEquals( false, file_exists( $csv->getResource() ) );
	}


	public function testExistingFile()
	{
		$csv = new MW_Container_Content_CSV( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile.csv', 'test.txt' );

		$this->assertEquals( true, file_exists( $csv->getResource() ) );
	}


	public function testAdd()
	{
		$options = array(
			'csv-separator' => ';',
			'csv-enclosure' => ':',
			'csv-escape' => '\\',
		);

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile.csv';

		$csv = new MW_Container_Content_CSV( $path, 'temp.txt', $options );

		$data = array(
			array( 'test', 'file', 'data' ),
			array( ':', pack( 'x' ), '\\' ),
		);

		foreach( $data as $entry ) {
			$csv->add( $entry );
		}

		$expected = ':test:;:file:;:data:' . PHP_EOL . ':\\::;:' . pack( 'x' ) . ':;:\\:' . PHP_EOL;

		if( ( $actual = file_get_contents( $csv->getResource() ) ) === false ) {
			throw new Exception( sprintf( 'Unable to get content of file "%1$s"', $csv->getResource() ) );
		}

		unlink( $csv->getResource() );

		$this->assertEquals( $expected, $actual );
	}


	public function testIterator()
	{
		$csv = new MW_Container_Content_CSV( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile.csv', 'test.txt' );

		$expected = array(
			array( 'test', 'file', 'data' ),
			array( '"', ',', '\\' ),
		);

		$actual = array();
		foreach( $csv as $entry ) {
			$actual[] = $entry;
		}

		$this->assertEquals( $expected, $actual );
	}

}

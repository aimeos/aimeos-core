<?php

/**
 * Test class for MW_Container_Zip.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Container_ZipTest extends MW_Unittest_Testcase
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
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile.zip';

		$zip = new MW_Container_Zip( $filename, 'CSV' );
		$zip->add( $zip->create( 'test.txt' ) );
		$zip->close();

		$check = file_exists( $filename );
		unlink( $filename );

		$this->assertEquals( true, $check );
		$this->assertEquals( false, file_exists( $filename ) );
	}


	public function testAdd()
	{
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile.zip';

		$zip = new MW_Container_Zip( $filename, 'CSV' );

		$content = $zip->create( 'test.txt' );
		$content->add( array( 'test', 'file', 'data' ) );

		$zip->add( $content );
		$zip->close();

		$za = new ZipArchive();
		$za->open( $filename );
		$actual = $za->getFromName( 'test.txt' );
		$za->close();

		$expected = '"test","file","data"' . PHP_EOL;

		unlink( $filename );

		$this->assertEquals( $expected, $actual );
	}


	public function testIterator()
	{
		$zip = new MW_Container_Zip( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile.zip', 'CSV' );

		$expected = array(
			'tempfile.csv' => 2,
			'testfile.csv' => 2,
		);

		$actual = array();
		foreach( $zip as $entry )
		{
			$rows = array();
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			// test if rewind or reopen works
			$rows = array();
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			$actual[ $entry->getName() ] = count( $rows );
		}

		$this->assertEquals( $expected, $actual );
	}

}

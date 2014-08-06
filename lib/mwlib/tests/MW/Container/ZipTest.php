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
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile';

		$zip = new MW_Container_Zip( $filename, 'CSV' );
		$zip->add( $zip->create( 'test' ) );
		$zip->close();

		$check = file_exists( $zip->getName() );
		unlink( $zip->getName() );

		$this->assertTrue( $check );
		$this->assertEquals( '.zip', substr( $zip->getName(), -4 ) );
		$this->assertFalse( file_exists( $zip->getName() ) );
	}


	public function testAdd()
	{
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile';

		$zip = new MW_Container_Zip( $filename, 'CSV' );

		$content = $zip->create( 'test' );
		$content->add( array( 'test', 'file', 'data' ) );

		$zip->add( $content );
		$zip->close();

		$za = new ZipArchive();
		$za->open( $zip->getName() );
		$actual = $za->getFromName( $content->getName() );
		$za->close();

		$expected = '"test","file","data"' . PHP_EOL;

		unlink( $zip->getName() );

		$this->assertEquals( $expected, $actual );
	}


	public function testGet()
	{
		$zip = new MW_Container_Zip( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile', 'CSV' );

		$this->assertInstanceOf( 'MW_Container_Content_Interface', $zip->get( 'tempfile.csv' ) );
	}


	public function testIterator()
	{
		$zip = new MW_Container_Zip( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile', 'CSV' );

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

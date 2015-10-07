<?php

namespace Aimeos\MW\Container\Content;


/**
 * Test class for \Aimeos\MW\Container\Content\CSV.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class CSVTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if( !is_dir( 'tmp' ) ) {
			mkdir( 'tmp', 0755 );
		}
	}


	public function testNewFile()
	{
		$csv = new \Aimeos\MW\Container\Content\CSV( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile', 'temp' );

		$check = file_exists( $csv->getResource() );
		unlink( $csv->getResource() );

		$this->assertEquals( true, $check );
		$this->assertEquals( false, file_exists( $csv->getResource() ) );
	}


	public function testExistingFile()
	{
		$csv = new \Aimeos\MW\Container\Content\CSV( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile.csv', 'test' );

		$this->assertEquals( true, file_exists( $csv->getResource() ) );
	}


	public function testAdd()
	{
		$options = array(
			'csv-separator' => ';',
			'csv-enclosure' => ':',
			'csv-escape' => '\\',
			'csv-lineend' => "\r\n",
			'csv-lineend-subst' => " ",
		);

		$path = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'tempfile';

		$csv = new \Aimeos\MW\Container\Content\CSV( $path, 'temp', $options );

		$data = array(
			array( 'test', 'file', 'data' ),
			array( ":\r\n", "\0", "\\" ),
		);

		foreach( $data as $entry ) {
			$csv->add( $entry );
		}
		$csv->close();

		$expected = ":test:;:file:;:data:\r\n:\\: :;:\0:;:\\:\r\n";

		if( ( $actual = file_get_contents( $csv->getResource() ) ) === false ) {
			throw new \Exception( sprintf( 'Unable to get content of file "%1$s"', $csv->getResource() ) );
		}

		unlink( $csv->getResource() );

		$this->assertEquals( $expected, $actual );
	}


	public function testIterator()
	{
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'testfile.csv';
		$csv = new \Aimeos\MW\Container\Content\CSV( $filename, 'test' );

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

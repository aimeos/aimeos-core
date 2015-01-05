<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class MW_Container_Content_TextTest extends MW_Unittest_Testcase
{
	public function testNewFile()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';
		$file = new MW_Container_Content_Binary( $filename, 'temp' );

		$check = file_exists( $file->getResource() );
		unlink( $file->getResource() );

		$this->assertEquals( true, $check );
		$this->assertEquals( false, file_exists( $file->getResource() ) );
	}


	public function testExistingFile()
	{
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfile';
		$file = new MW_Container_Content_Binary( $filename, 'testfile' );

		$this->assertEquals( true, file_exists( $file->getResource() ) );
	}


	public function testAdd()
	{
		$options = array(
			'bin-maxsize' => 0xFF,
		);

		$path = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';

		$file = new MW_Container_Content_Binary( $path, 'temp', $options );
		$file->add( 'test text' );
		$file->close();

		$expected = "test text";
		$actual = file_get_contents( $file->getResource() );

		unlink( $file->getResource() );

		$this->assertEquals( $expected, $actual );
		$this->assertEquals( $path, $file->getResource() );
	}


	public function testIterator()
	{
		$path = __DIR__ . DIRECTORY_SEPARATOR . 'testfile';
		$file = new MW_Container_Content_Binary( $path, 'test' );

		$expected = array( 'test data' );

		$actual = array();
		foreach( $file as $entry ) {
			$actual[] = $entry;
		}

		$this->assertEquals( $expected, $actual );
	}
}

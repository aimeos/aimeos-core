<?php

namespace Aimeos\MW\Container\Content;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class GzipTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		if( !is_dir( 'tmp' ) ) {
			mkdir( 'tmp', 0755 );
		}
	}


	public function testNewFile()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp' );

		$check = file_exists( $file->getResource() );
		unlink( $file->getResource() );

		$this->assertEquals( true, $check );
		$this->assertEquals( false, file_exists( $file->getResource() ) );
	}


	public function testExistingFile()
	{
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfile';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'test' );

		$this->assertEquals( true, file_exists( $file->getResource() ) );
	}


	public function testAdd()
	{
		$options = array(
			'gzip-level' => 9,
		);

		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp', $options );
		$file->add( 'test text' );
		$file->close();

		$expected = '1f8b080000000000';
		$actual = file_get_contents( $file->getResource() );

		unlink( $file->getResource() );

		$this->assertStringStartsWith( $expected, bin2hex( $actual ) );
		$this->assertEquals( $filename . '.gz', $file->getResource() );
	}


	public function testIterator()
	{
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfile';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'test' );

		$expected = array( "test data" );

		$actual = [];
		foreach( $file as $entry ) {
			$actual[] = $entry;
		}

		$this->assertEquals( $expected, $actual );
	}

}

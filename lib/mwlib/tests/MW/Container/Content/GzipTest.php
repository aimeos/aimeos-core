<?php

namespace Aimeos\MW\Container\Content;


/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class GzipTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( !is_dir( 'tmp' ) ) {
			mkdir( 'tmp', 0755 );
		}
	}


	public function testNewFile()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile.gz';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp', ['gzip-mode' => 'wb'] );
		$resource = $file->getResource();
		$file->close();

		$this->assertEquals( true, file_exists( $resource ) );
		unlink( $resource );
	}


	public function testExistingFile()
	{
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfile.gz';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'test' );

		$this->assertEquals( true, file_exists( $file->getResource() ) );

		$file->close();
	}


	public function testAdd()
	{
		$options = array(
			'gzip-mode' => 'wb',
			'gzip-level' => 9,
		);

		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp', $options );
		$file->add( 'test text' );
		$file->close();

		$actual = file_get_contents( $file->getResource() );
		unlink( $file->getResource() );

		$this->assertStringStartsWith( '1f8b080000000000', bin2hex( $actual ) );
		$this->assertEquals( $filename . '.gz', $file->getResource() );
	}


	public function testOverwrite()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile.gz';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp', ['gzip-mode' => 'wb'] );
		$file->add( 'test text' );
		$file->close();

		$data1 = file_get_contents( $file->getResource() );

		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'temp', ['gzip-mode' => 'wb'] );
		$file->add( 'test 2 text' );
		$file->close();

		$data2 = file_get_contents( $file->getResource() );

		unlink( $file->getResource() );

		$this->assertNotEquals( $data1, $data2 );
	}


	public function testIterator()
	{
		$filename = __DIR__ . DIRECTORY_SEPARATOR . 'testfile.gz';
		$file = new \Aimeos\MW\Container\Content\Gzip( $filename, 'test' );

		$expected = array( "test data" );

		$actual = [];
		foreach( $file as $entry ) {
			$actual[] = $entry;
		}

		$file->close();

		$this->assertEquals( $expected, $actual );
	}

}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */

namespace Aimeos\MW\Container;


class FileTest extends \PHPUnit\Framework\TestCase
{
	public function testNewFile()
	{
		$filepath = 'tmp' . DIRECTORY_SEPARATOR . 'testdir' . DIRECTORY_SEPARATOR . 'testfile.csv';

		$file = new \Aimeos\MW\Container\File( $filepath, 'CSV' );
		$file->add( $file->create( 'test' ) );
		$file->close();

		$filepath = $file->getName();

		$check = file_exists( $filepath );
		unlink( $filepath );

		$this->assertTrue( $check );
		$this->assertEquals( $filepath, $file->getName() );
		$this->assertFalse( file_exists( $filepath ) );
	}


	public function testAdd()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = new \Aimeos\MW\Container\File( 'tmp' . $ds . 'testdir' . $ds . 'testfile.csv', 'CSV' );

		$content = $file->create( 'test' );
		$content->add( array( 'test', 'file', 'data' ) );

		$file->add( $content );
		$file->close();

		$filepath = $file->getName();

		$actual = file_get_contents( $filepath );
		$expected = '"test","file","data"' . "\n";

		unlink( $filepath );

		$this->assertEquals( $expected, $actual );
		$this->assertFalse( file_exists( $filepath ) );
	}


	public function testGet()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = new \Aimeos\MW\Container\File( __DIR__ . $ds . '_testdir' . $ds . 'testfile.csv', 'CSV' );

		$this->assertInstanceOf( \Aimeos\MW\Container\Content\Iface::class, $file->get( 'testfile.csv' ) );
	}


	public function testIterator()
	{
		$ds = DIRECTORY_SEPARATOR;
		$file = new \Aimeos\MW\Container\File( __DIR__ . $ds . '_testdir' . $ds . 'testfile.csv', 'CSV' );

		$expected = array(
			'testfile.csv' => 1,
		);

		$actual = [];
		foreach( $file as $entry )
		{
			$rows = [];
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			// test if rewind works
			$rows = [];
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			$actual[$entry->getName()] = count( $rows );
		}

		$this->assertEquals( $expected, $actual );
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\MW\Container;


class DirectoryTest extends \PHPUnit\Framework\TestCase
{
	public function testNewFile()
	{
		$dirname = 'tmp' . DIRECTORY_SEPARATOR . 'testdir';

		$dir = new \Aimeos\MW\Container\Directory( $dirname, 'CSV' );
		$dir->add( $dir->create( 'test' ) );
		$dir->close();

		$filepath = $dir->getName() . DIRECTORY_SEPARATOR . 'test.csv';

		$check = file_exists( $filepath );
		unlink( $filepath );
		rmdir( $dirname );

		$this->assertTrue( $check );
		$this->assertEquals( $dirname, $dir->getName() );
		$this->assertFalse( file_exists( $dirname ) );
	}


	public function testAdd()
	{
		$dir = new \Aimeos\MW\Container\Directory( 'tmp' . DIRECTORY_SEPARATOR . 'testdir', 'CSV' );

		$content = $dir->create( 'test' );
		$content->add( array( 'test', 'file', 'data' ) );

		$dir->add( $content );
		$dir->close();

		$filepath = $dir->getName() . DIRECTORY_SEPARATOR . 'test.csv';

		$actual = file_get_contents( $filepath );
		$expected = '"test","file","data"' . "\n";

		unlink( $filepath );

		$this->assertEquals( $expected, $actual );
		$this->assertFalse( file_exists( $filepath ) );
	}


	public function testGet()
	{
		$dir = new \Aimeos\MW\Container\Directory( __DIR__ . DIRECTORY_SEPARATOR . '_testdir', 'CSV' );

		$this->assertInstanceOf( \Aimeos\MW\Container\Content\Iface::class, $dir->get( 'testfile.csv' ) );
	}


	public function testIterator()
	{
		$dir = new \Aimeos\MW\Container\Directory( __DIR__ . DIRECTORY_SEPARATOR . '_testdir', 'CSV' );

		$expected = array(
			'testfile.csv' => 1,
		);

		$actual = [];
		foreach( $dir as $entry )
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

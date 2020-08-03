<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2020
 */


namespace Aimeos\MW\Container;


class ZipTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( !is_dir( 'tmp' ) ) {
			mkdir( 'tmp', 0755 );
		}
	}


	public function testNewFile()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';

		$zip = new \Aimeos\MW\Container\Zip( $filename, 'CSV' );
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
		$filename = 'tmp' . DIRECTORY_SEPARATOR . 'tempfile';

		$zip = new \Aimeos\MW\Container\Zip( $filename, 'CSV' );

		$content = $zip->create( 'test' );
		$content->add( array( 'test', 'file', 'data' ) );

		$zip->add( $content );
		$zip->close();

		$za = new \ZipArchive();
		$za->open( $zip->getName() );
		$actual = $za->getFromName( $content->getName() );
		$za->close();

		$expected = '"test","file","data"' . PHP_EOL;

		unlink( $zip->getName() );

		$this->assertEquals( $expected, $actual );
	}


	public function testGet()
	{
		$zip = new \Aimeos\MW\Container\Zip( __DIR__ . DIRECTORY_SEPARATOR . 'testfile', 'CSV' );

		$this->assertInstanceOf( \Aimeos\MW\Container\Content\Iface::class, $zip->get( 'tempfile.csv' ) );
	}


	public function testIterator()
	{
		$zip = new \Aimeos\MW\Container\Zip( __DIR__ . DIRECTORY_SEPARATOR . 'testfile', 'CSV' );

		$expected = array(
			'tempfile.csv' => 2,
			'testfile.csv' => 2,
		);

		$actual = [];
		foreach( $zip as $entry )
		{
			$rows = [];
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			// test if rewind or reopen works
			$rows = [];
			foreach( $entry as $row ) {
				$rows[] = $row;
			}

			$actual[$entry->getName()] = count( $rows );
		}

		$this->assertEquals( $expected, $actual );
	}

}

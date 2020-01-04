<?php

namespace Aimeos\MW\Filesystem;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $basedir;
	private $object;


	protected function setUp() : void
	{
		$this->basedir = __DIR__ . '/../../tmp/';
		$this->object = new \Aimeos\MW\Filesystem\Standard( array( 'basedir' => $this->basedir ) );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testIsdir()
	{
		$this->assertTrue( $this->object->isdir( '' ) );
	}


	public function testMkdir()
	{
		$object = $this->object->mkdir( 'fstest' );
		$result = is_dir( $this->basedir . 'fstest' );
		rmdir( $this->basedir . 'fstest' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\DirIface::class, $object );
		$this->assertTrue( $result );
	}


	public function testMkdirException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->mkdir( '' );
	}


	public function testRmdir()
	{
		mkdir( $this->basedir . 'fstest2' );

		$object = $this->object->rmdir( 'fstest2' );
		$result = is_dir( $this->basedir . 'fstest2' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\DirIface::class, $object );
		$this->assertFalse( $result );
	}


	public function testRmdirException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->rmdir( 'rmdirinvalid' );
	}


	public function testScan()
	{
		touch( $this->basedir . 'file1' );

		$result = $this->object->scan();

		$this->assertInstanceof( 'Iterator', $result );

		$list = [];
		foreach( $result as $entry ) {
			$list[] = (string) $entry;
		}
		unlink( $this->basedir . 'file1' );

		$this->assertTrue( in_array( 'file1', $list ) );
	}


	public function testScanException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->scan( 'scaninvalid' );
	}


	public function testSize()
	{
		file_put_contents( $this->basedir . 'file2', 'test' );

		$result = $this->object->size( 'file2' );

		unlink( $this->basedir . 'file2' );

		$this->assertEquals( 4, $result );
	}


	public function testSizeException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->size( 'sizeinvalid' );
	}


	public function testTime()
	{
		touch( $this->basedir . 'file4' );

		$result = $this->object->time( 'file4' );

		unlink( $this->basedir . 'file4' );

		$this->assertGreaterThan( 0, $result );
	}


	public function testTimeException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->time( 'timeinvalid' );
	}


	public function testRm()
	{
		touch( $this->basedir . 'file5' );

		$object = $this->object->rm( 'file5' );

		$result = file_exists( $this->basedir . 'file5' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertFalse( $result );
	}


	public function testRmException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->rm( 'rminvalid' );
	}


	public function testHas()
	{
		touch( $this->basedir . 'file6' );

		$result = $this->object->has( 'file6' );

		unlink( $this->basedir . 'file6' );

		$this->assertTrue( $result );
		$this->assertFalse( $this->object->has( 'fsinvalid' ) );
	}


	public function testRead()
	{
		file_put_contents( $this->basedir . 'file7', 'test' );

		$result = $this->object->read( 'file7' );

		unlink( $this->basedir . 'file7' );

		$this->assertEquals( 'test', $result );
	}


	public function testReadException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->read( 'readinvalid' );
	}


	public function testReadf()
	{
		file_put_contents( $this->basedir . 'file77', 'test' );

		$result = $this->object->readf( 'file77' );

		$this->assertTrue( file_exists( $result ) );

		unlink( $result );
	}


	public function testReadfException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->readf( 'readinvalid' );
	}


	public function testReads()
	{
		file_put_contents( $this->basedir . 'file8', 'test' );

		$handle = $this->object->reads( 'file8' );
		$result = fgets( $handle );
		fclose( $handle );

		unlink( $this->basedir . 'file8' );

		$this->assertEquals( 'test', $result );
	}


	public function testReadsException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->reads( 'readsinvalid' );
	}


	public function testWrite()
	{
		$object = $this->object->write( 'file9', 'test' );

		$result = file_get_contents( $this->basedir . 'file9' );
		unlink( $this->basedir . 'file9' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertEquals( 'test', $result );
	}


	public function testWriteException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->write( '', 'test' );
	}


	public function testWritef()
	{
		$file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'file99';
		file_put_contents( $file, 'test' );

		$object = $this->object->writef( 'file99', $file );

		$result = file_get_contents( $this->basedir . 'file99' );
		unlink( $this->basedir . 'file99' );
		unlink( $file );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertEquals( 'test', $result );
	}


	public function testWritefException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->writef( '', 'test' );
	}


	public function testWrites()
	{
		if( ( $handle = fopen( $this->basedir . 'file10tmp', 'w+' ) ) === false ) {
			throw new \RuntimeException( sprintf( 'Failed opening file "%1$s"', $this->basedir . 'file10tmp' ) );
		}

		fwrite( $handle, 'test' );
		rewind( $handle );

		$object = $this->object->writes( 'file10', $handle );

		fclose( $handle );
		$result = file_get_contents( $this->basedir . 'file10' );
		unlink( $this->basedir . 'file10tmp' );
		unlink( $this->basedir . 'file10' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertEquals( 'test', $result );
	}


	public function testWritesException()
	{
		$this->expectException( \Aimeos\MW\Filesystem\Exception::class );
		$this->object->writes( 'file10ex', null );
	}


	public function testMove()
	{
		touch( $this->basedir . 'file11' );

		$object = $this->object->move( 'file11', 'file11move' );

		$result = file_exists( $this->basedir . 'file11move' );
		$result2 = file_exists( $this->basedir . 'file11' );

		unlink( $this->basedir . 'file11move' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertTrue( $result );
		$this->assertFalse( $result2 );
	}


	public function testCopy()
	{
		touch( $this->basedir . 'file12' );

		$object = $this->object->copy( 'file12', 'file12copy' );

		$result = file_exists( $this->basedir . 'file12copy' );
		$result2 = file_exists( $this->basedir . 'file12' );

		unlink( $this->basedir . 'file12copy' );
		unlink( $this->basedir . 'file12' );

		$this->assertInstanceOf( \Aimeos\MW\Filesystem\Iface::class, $object );
		$this->assertTrue( $result );
		$this->assertTrue( $result2 );
	}
}

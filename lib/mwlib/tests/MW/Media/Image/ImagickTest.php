<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MW\Media\Image;


class ImagickTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		if( !class_exists( \Imagick::class ) ) {
			$this->markTestSkipped( 'Imagick not available' );
		}
	}


	public function testConstructGif()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/gif', [] );

		$this->assertEquals( 'image/gif', $media->getMimetype() );
	}


	public function testConstructJpeg()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.jpg' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/jpeg', [] );

		$this->assertEquals( 'image/jpeg', $media->getMimetype() );
	}


	public function testConstructPng()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );

		$this->assertEquals( 'image/png', $media->getMimetype() );
	}


	public function testConstructImageException()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'application.txt' );

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		new \Aimeos\MW\Media\Image\Imagick( $content, 'text/plain', [] );
	}


	public function testGetHeight()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.jpg' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/jpeg', [] );

		$this->assertEquals( 10, $media->getHeight() );
	}


	public function testGetWidth()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.jpg' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/jpeg', [] );

		$this->assertEquals( 10, $media->getWidth() );
	}


	public function testSaveGif()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.gif';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );
		$media->save( $dest, 'image/gif' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSaveGifInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.gif';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		$media->save( $dest, 'image/gif' );
	}


	public function testSaveJpeg()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.jpg';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/gif', [] );
		$media->save( $dest, 'image/jpeg' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSaveJpegInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.jpg';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		$media->save( $dest, 'image/jpeg' );
	}


	public function testSavePng()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif' );
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.png';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/gif', [] );
		$media->save( $dest, 'image/png' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSavePngInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif' );
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.png';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/gif', [] );

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		$media->save( $dest, 'image/png' );
	}


	public function testSaveWatermarkNotfound()
	{
		$ds = DIRECTORY_SEPARATOR;
		$basedir = dirname( __DIR__ );

		$content = file_get_contents( $basedir . $ds . '_testfiles' . $ds . 'image.jpg' );
		$options = ['image' => ['watermark' => $basedir . $ds . 'notexisting' . $ds . 'image.png']];

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', $options );
	}


	public function testSaveWatermarkInvalid()
	{
		$ds = DIRECTORY_SEPARATOR;
		$basedir = dirname( __DIR__ );

		$content = file_get_contents( $basedir . $ds . '_testfiles' . $ds . 'image.jpg' );
		$options = ['image' => ['watermark' => $basedir . $ds . '_testfiles' . $ds . 'application.txt']];

		$this->expectException( \Aimeos\MW\Media\Exception::class );
		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', $options );
	}


	public function testSaveWatermark()
	{
		$ds = DIRECTORY_SEPARATOR;
		$basedir = dirname( __DIR__ );

		$content = file_get_contents( $basedir . $ds . '_testfiles' . $ds . 'image.jpg' );
		$options = ['image' => ['watermark' => $basedir . $ds . '_testfiles' . $ds . 'image.png']];
		$dest = dirname( dirname( $basedir ) ) . $ds . 'tmp' . $ds . 'media.jpg';

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', $options );
		$media->save( $dest, 'image/jpeg' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testScaleFit()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );
		$info = getimagesizefromstring( $media->scale( 100, 100, 1 )->save( null, 'image/png' ) );

		$this->assertEquals( 100, $info[0] );
		$this->assertEquals( 100, $info[1] );
	}


	public function testScaleDown()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );
		$info = getimagesizefromstring( $media->scale( 5, 100 )->save( null, 'image/png' ) );

		$this->assertEquals( 5, $info[0] );
		$this->assertEquals( 5, $info[1] );
	}


	public function testScaleInside()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );
		$info = getimagesizefromstring( $media->scale( 100, 100 )->save( null, 'image/png' ) );

		$this->assertEquals( 10, $info[0] );
		$this->assertEquals( 10, $info[1] );
	}


	public function testScaleFitCrop()
	{
		$ds = DIRECTORY_SEPARATOR;
		$content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png' );

		$media = new \Aimeos\MW\Media\Image\Imagick( $content, 'image/png', [] );
		$info = getimagesizefromstring( $media->scale( 100, 50, 2 )->save( null, 'image/png' ) );

		$this->assertEquals( 100, $info[0] );
		$this->assertEquals( 50, $info[1] );
	}
}

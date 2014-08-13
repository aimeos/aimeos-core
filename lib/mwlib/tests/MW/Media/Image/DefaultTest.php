<?php

/**
 * Test class for MW_Media_Image_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Media_Image_DefaultTest extends MW_Unittest_Testcase
{
	public function testConstructGif()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif';

		$media = new MW_Media_Image_Default( $filename, 'image/gif', array() );

		$this->assertEquals( 'image/gif', $media->getMimetype() );
	}


	public function testConstructJpeg()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.jpg';

		$media = new MW_Media_Image_Default( $filename, 'image/jpeg', array() );

		$this->assertEquals( 'image/jpeg', $media->getMimetype() );
	}


	public function testConstructPng()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );

		$this->assertEquals( 'image/png', $media->getMimetype() );
	}


	public function testConstructFileException()
	{
		$this->setExpectedException( 'MW_Media_Exception' );
		new MW_Media_Image_Default( 'notexisting', '', array() );
	}


	public function testConstructImageException()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'application.txt';

		$this->setExpectedException( 'MW_Media_Exception' );
		new MW_Media_Image_Default( $filename, 'text/plain', array() );
	}


	public function testDestruct()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );

		unset( $media );
	}


	public function testSaveGif()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.gif';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );
		$media->save( $dest, 'image/gif' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSaveGifInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.gif';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );

		$this->setExpectedException( 'MW_Media_Exception' );
		$media->save( $dest, 'image/gif' );
	}


	public function testSaveJpeg()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.jpg';

		$media = new MW_Media_Image_Default( $filename, 'image/gif', array() );
		$media->save( $dest, 'image/jpeg' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSaveJpegInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.jpg';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );

		$this->setExpectedException( 'MW_Media_Exception' );
		$media->save( $dest, 'image/jpeg' );
	}


	public function testSavePng()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.png';

		$media = new MW_Media_Image_Default( $filename, 'image/gif', array() );
		$media->save( $dest, 'image/png' );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSavePngInvalidDest()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.gif';
		$dest = __DIR__ . $ds . 'notexisting' . $ds . 'media.png';

		$media = new MW_Media_Image_Default( $filename, 'image/gif', array() );

		$this->setExpectedException( 'MW_Media_Exception' );
		$media->save( $dest, 'image/png' );
	}


	public function testScale()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.png';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );
		$media->scale( 100, 100, false );
		$media->save( $dest, 'image/png' );

		$info = getimagesize( $dest );
		unlink( $dest );

		$this->assertEquals( 100, $info[0] );
		$this->assertEquals( 100, $info[1] );
	}


	public function testScaleFit()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.png';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );
		$media->scale( 5, 100 );
		$media->save( $dest, 'image/png' );

		$info = getimagesize( $dest );
		unlink( $dest );

		$this->assertEquals( 5, $info[0] );
		$this->assertEquals( 5, $info[1] );
	}


	public function testScaleFitInside()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.png';
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.png';

		$media = new MW_Media_Image_Default( $filename, 'image/png', array() );
		$media->scale( 100, 100 );
		$media->save( $dest, 'image/png' );

		$info = getimagesize( $dest );
		unlink( $dest );

		$this->assertEquals( 10, $info[0] );
		$this->assertEquals( 10, $info[1] );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MW\Media\Image;


class SvgTest extends \PHPUnit\Framework\TestCase
{
	private $content;


	protected function setUp() : void
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->content = file_get_contents( dirname( __DIR__ ) . $ds . '_testfiles' . $ds . 'image.svgz' );
	}


	public function testConstruct()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );

		$this->assertEquals( 'image/svg+xml', $media->getMimetype() );
	}


	public function testConstructException()
	{
		$this->expectException( \Aimeos\MW\Media\Exception::class );
		new \Aimeos\MW\Media\Image\Svg( 'test', 'text/plain', [] );
	}


	public function testGetHeight()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );

		$this->assertEquals( 300, $media->getHeight() );
	}


	public function testGetWidth()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );

		$this->assertEquals( 200, $media->getWidth() );
	}


	public function testSave()
	{
		$ds = DIRECTORY_SEPARATOR;
		$dest = dirname( dirname( dirname( __DIR__ ) ) ) . $ds . 'tmp' . $ds . 'media.svg';

		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );
		$media->save( $dest );

		$this->assertEquals( true, file_exists( $dest ) );
		unlink( $dest );
	}


	public function testSaveContent()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );
		$result = $media->save();

		$this->assertStringStartsWith( '<?xml version="1.0" standalone="yes"?>', $result );
	}


	public function testScale()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );
		$media = $media->scale( 100, 100, false );

		$this->assertEquals( 100, $media->getHeight() );
		$this->assertEquals( 100, $media->getWidth() );
	}


	public function testScaleFit()
	{
		$media = new \Aimeos\MW\Media\Image\Svg( $this->content, 'image/svg+xml', [] );
		$media = $media->scale( 150, 100, true );

		$this->assertEquals( 100, $media->getHeight() );
		$this->assertEquals( 67, $media->getWidth() );
	}
}

<?php

namespace Aimeos\MW\Jsb2\Standard;


/**
 * Test class for \Aimeos\MW\Jsb2\Standard.
 *
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */
class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $manifestPath;


	protected function setUp() : void
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->manifestPath = __DIR__ . $ds . 'manifests' . $ds;
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest.jsb2' );
	}


	public function testConstructNoIncludeFilesExceptions()
	{
		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_fileinclude.jsb2' );
	}


	public function testConstructNoPackageExceptions()
	{
		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_package.jsb2' );
	}


	public function testConstructInvalidPackageContentExceptions()
	{
		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_package_content.jsb2' );
	}


	public function testConstructNotJSONExceptions()
	{
		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_no_json.jsb2' );
	}


	public function testConstructFileNotExistingExceptions()
	{
		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_not_existing.jsb2' );
	}


	public function testGetFiles()
	{
		$files = $this->object->getFiles( 'js' );

		$this->assertEquals( 1, count( $files ) );
		$this->assertStringContainsString( 'test.js', $files[0] );
	}


	public function testGetHTML()
	{
		$html = '<script type="text/javascript" src="/./../%1$s"></script>';
		$mtime = filemtime( __DIR__ . DIRECTORY_SEPARATOR . 'test.js' );

		$this->assertEquals( sprintf( $html, 'test.js?v=' . $mtime ), trim( $this->object->getHTML( 'js' ) ) );
	}


	public function testGetUrls()
	{
		$urls = $this->object->getUrls( 'js' );

		$this->assertEquals( 1, count( $urls ) );
		$this->assertStringContainsString( '/test.js', $urls[0] );
	}


	public function testGetUrlsFilemtimeException()
	{
		$object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_filemtime_exception.jsb2' );

		$this->expectException( '\Aimeos\MW\Jsb2\Exception' );
		$object->getHTML( 'js' );
	}
}

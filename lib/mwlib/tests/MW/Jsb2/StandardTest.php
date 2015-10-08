<?php

namespace Aimeos\MW\Jsb2\Standard;


/**
 * Test class for \Aimeos\MW\Jsb2\Standard.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class Test extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $manifestPath;
	private $deployPath;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->manifestPath = __DIR__ . $ds . 'manifests' . $ds;
		$this->deployPath = __DIR__ . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' . $ds;
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest.jsb2' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->delTree( __DIR__ . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' );
	}


	public function testConstructNoIncludeFilesExceptions()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_fileinclude.jsb2' );
	}


	public function testConstructNoPackageExceptions()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_package.jsb2' );
	}


	public function testConstructInvalidPackageContentExceptions()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_invalid_package_content.jsb2' );
	}


	public function testConstructNotJSONExceptions()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_no_json.jsb2' );
	}


	public function testConstructFileNotExistingExceptions()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_not_existing.jsb2' );
	}


	public function testDeploy()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->object->deploy();
		$this->assertFileExists( $this->deployPath . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0755', substr( decoct( fileperms( $this->deployPath ) ), 1 ) );
		$this->assertEquals( '0644',
			substr( decoct( fileperms( $this->deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);
	}


	public function testDeployPermissionReset()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->object->deploy( null, true, 0645, 0754 );
		$this->assertFileExists( $this->deployPath . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0754', substr( decoct( fileperms( $this->deployPath ) ), 1 ) );
		$this->assertEquals( '0645',
			substr( decoct( fileperms( $this->deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);

		$this->object->deploy( null, true, 0655, 0755 );
		$this->assertFileExists( $this->deployPath . $ds . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0754', substr( decoct( fileperms( $this->deployPath ) ), 1 ) );
		$this->assertEquals( '0655',
			substr( decoct( fileperms( $this->deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);

	}


	public function testDeployFiletypeNoDebug()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->object->deploy( 'js', false );
		$this->assertFileExists( $this->deployPath . 'js' . $ds . 'jsb2-test.js' );
	}


	public function testGetUrlsWithPackage()
	{
		$this->object->deploy();
		$urls = $this->object->getUrls( 'js' );

		$this->assertEquals( 1, count( $urls ) );
		$this->assertContains( '/jsb2-test.js', $urls[0] );
	}


	public function testGetUrlsWithoutPackage()
	{
		$urls = $this->object->getUrls( 'js' );

		$this->assertEquals( 1, count( $urls ) );
		$this->assertContains( '/test.js', $urls[0] );
	}


	public function testGetHTMLWithPackage()
	{
		$this->object->deploy();
		$this->assertGreaterThan( 1, strpos( trim( $this->object->getHTML( 'js' ) ), 'jsb2/js/jsb2-test.js' ) );
		$this->assertEquals( '', $this->object->getHTML( 'css' ) );
	}


	public function testGetHTMLWithoutPackage()
	{
		$html = '<script type="text/javascript" src="/./../%1$s"></script>';
		$mtime = filemtime( __DIR__ . DIRECTORY_SEPARATOR . 'test.js' );

		$this->assertEquals( sprintf( $html, 'test.js?v=' . $mtime ), trim( $this->object->getHTML( 'js' ) ) );
	}


	public function testGetHTMLFilemtimeException()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = __DIR__ . $ds . 'test.js';
		$alteredFilename = __DIR__ . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' . 'test.js';

		copy( $filename, $alteredFilename );
		$this->object = new \Aimeos\MW\Jsb2\Standard( $this->manifestPath . 'manifest_filemtime_exception.jsb2' );
		unlink( $alteredFilename );

		$this->setExpectedException( '\\Aimeos\\MW\\Jsb2\\Exception' );
		$this->object->getHTML( 'js' );
	}


	/**
	 * @param string $dir
	 */
	protected function delTree( $dir )
	{
		if( !is_dir( $dir ) ) {
			return;
		}

		$dirIterator = new \DirectoryIterator( $dir );

		foreach( $dirIterator as $iterator )
		{
			if( $iterator->isDot() ) {
				continue;
			}

			if( $iterator->isDir() ) {
				$this->delTree( $iterator->getPathname() );
			} else {
				unlink( $iterator->getPathname() );
			}
		}

		rmdir( $dir );
	}
}
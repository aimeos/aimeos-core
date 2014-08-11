<?php

/**
 * Test class for MW_Jsb2_Default.
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 */
class MW_Jsb2_Default_Test extends MW_Unittest_Testcase
{
	private $_object;
	private $_manifestPath;
	private $_deployPath;

	/**
	 * @param string $dir
	 */
	protected function _delTree( $dir )
	{
		if( !is_dir( $dir ) ) {
			return;
		}

		$dirIterator = new DirectoryIterator( $dir );

		foreach( $dirIterator as $iterator )
		{
			if( $iterator->isDot() ) {
				continue;
			}

			if( $iterator->isDir() )
			{
				$this->_delTree( $iterator->getPathname() );
			}
			else
			{
				unlink( $iterator->getPathname() );
			}
		}

		rmdir( $dir );
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$ds = DIRECTORY_SEPARATOR;
		$this->_manifestPath = dirname( __FILE__ ) . $ds . 'manifests' . $ds;
		$this->_deployPath = dirname( __FILE__ ) . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' . $ds;
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest.jsb2' );
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

		$this->_delTree( dirname( __FILE__ ) . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' );
	}


	public function testConstructNoIncludeFilesExceptions()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_invalid_fileinclude.jsb2' );
	}


	public function testConstructNoPackageExceptions()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_invalid_package.jsb2' );
	}


	public function testConstructInvalidPackageContentExceptions()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_invalid_package_content.jsb2' );
	}


	public function testConstructNotJSONExceptions()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_no_json.jsb2' );
	}


	public function testConstructFileNotExistingExceptions()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_not_existing.jsb2' );
	}


	public function testDeploy()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->_object->deploy();
		$this->assertFileExists( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0755', substr( decoct( fileperms( $this->_deployPath ) ), 1 ) );
		$this->assertEquals( '0644',
			substr( decoct( fileperms( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);
	}


	public function testDeployPermissionReset()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->_object->deploy( null, true, 0645, 0754 );
		$this->assertFileExists( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0754', substr( decoct( fileperms( $this->_deployPath ) ), 1 ) );
		$this->assertEquals( '0645',
			substr( decoct( fileperms( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);

		$this->_object->deploy( null, true, 0655, 0755 );
		$this->assertFileExists( $this->_deployPath . $ds . 'js' . $ds . 'jsb2-test.js' );
		$this->assertEquals( '0754', substr( decoct( fileperms( $this->_deployPath ) ), 1 ) );
		$this->assertEquals( '0655',
			substr( decoct( fileperms( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' ) ), 1 )
		);

	}


	public function testDeployFiletypeNoDebug()
	{
		$ds = DIRECTORY_SEPARATOR;

		$this->_object->deploy( 'js', false );
		$this->assertFileExists( $this->_deployPath . 'js' . $ds . 'jsb2-test.js' );
	}


	public function testGetHTMLWithPackage()
	{
		$this->_object->deploy();
		$this->assertGreaterThan( 1, strpos( trim( $this->_object->getHTML() ), 'jsb2/js/jsb2-test.js' )  );
		$this->assertGreaterThan( 1, strpos( trim( $this->_object->getHTML( 'js' ) ), 'jsb2/js/jsb2-test.js' ) );
		$this->assertEquals( '', $this->_object->getHTML( 'css' ) );
	}


	public function testGetHTMLWithoutPackage()
	{
		$html = '<script type="text/javascript" src="/./../%1$s"></script>';
		$mtime = filemtime( __DIR__ . DIRECTORY_SEPARATOR . 'test.js' );

		$this->assertEquals( sprintf( $html, 'test.js?v=' . $mtime ), trim( $this->_object->getHTML( 'js' ) ) );
	}


	public function testGetHTMLUnknownFiletypeException()
	{
		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_unknown_type.jsb2', '' );
		$this->_object->getHTML();
	}


	public function testGetHTMLFilemtimeException()
	{
		$ds = DIRECTORY_SEPARATOR;
		$filename = dirname( __FILE__ ) . $ds . 'test.js';
		$alteredFilename = dirname( __FILE__ ) . $ds . '..' . $ds . '..' . $ds . 'tmp' . $ds . 'jsb2' . 'test.js';

		copy( $filename, $alteredFilename );
		$this->_object = new MW_Jsb2_Default( $this->_manifestPath . 'manifest_filemtime_exception.jsb2' );
		unlink( $alteredFilename );

		$this->setExpectedException( 'MW_Jsb2_Exception' );
		$this->_object->getHTML();
	}
}
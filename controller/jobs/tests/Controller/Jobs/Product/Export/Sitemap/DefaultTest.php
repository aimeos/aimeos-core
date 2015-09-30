<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Export_Sitemap_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		MShop_Factory::setCache( true );

		$this->_context = TestHelper::getContext();
		$this->_aimeos = TestHelper::getAimeos();

		$this->_object = new Controller_Jobs_Product_Export_Sitemap_Default( $this->_context, $this->_aimeos );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		MShop_Factory::setCache( false );
		MShop_Factory::clear();

		$this->_object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product site map', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Creates a product site map for search engines';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$this->_object->run();
		
		$ds = DIRECTORY_SEPARATOR;
		$this->assertFileExists( 'tmp' . $ds . 'aimeos-sitemap-1.xml.gz' );
		$this->assertFileExists( 'tmp' . $ds . 'aimeos-sitemap-2.xml.gz' );
		$this->assertFileExists( 'tmp' . $ds . 'aimeos-sitemap-index.xml.gz' );
		
		$file1 = gzread( gzopen( 'tmp' . $ds . 'aimeos-sitemap-1.xml.gz', 'rb' ), 0x1000 );
		$file2 = gzread( gzopen( 'tmp' . $ds . 'aimeos-sitemap-2.xml.gz', 'rb' ), 0x1000 );
		$index = gzread( gzopen( 'tmp' . $ds . 'aimeos-sitemap-index.xml.gz', 'rb' ), 0x1000 );

		unlink( 'tmp' . $ds . 'aimeos-sitemap-1.xml.gz' );
		unlink( 'tmp' . $ds . 'aimeos-sitemap-2.xml.gz' );
		unlink( 'tmp' . $ds . 'aimeos-sitemap-index.xml.gz' );

		$this->assertContains( 'Cafe-Noire-Expresso', $file1 );
		$this->assertContains( 'Unittest%3A-Bundle', $file2 );

		$this->assertContains( 'aimeos-sitemap-1.xml.gz', $index );
		$this->assertContains( 'aimeos-sitemap-2.xml.gz', $index );
	}
}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


class Controller_Jobs_Product_Export_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $_object;
	private $_context;
	private $_arcavias;


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
		$this->_arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Product_Export_Default( $this->_context, $this->_arcavias );
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
		$this->assertEquals( 'Product export', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Exports all available products';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$this->_object->run();
		
		$ds = DIRECTORY_SEPARATOR;
		$this->assertFileExists( 'tmp' . $ds . 'aimeos-products-1.xml' );
		$this->assertFileExists( 'tmp' . $ds . 'aimeos-products-2.xml' );
		
		$file1 = file_get_contents( 'tmp' . $ds . 'aimeos-products-1.xml' );
		$file2 = file_get_contents( 'tmp' . $ds . 'aimeos-products-2.xml' );

		unlink( 'tmp' . $ds . 'aimeos-products-1.xml' );
		unlink( 'tmp' . $ds . 'aimeos-products-2.xml' );

		$this->assertContains( 'CNE', $file1 );
		$this->assertContains( 'U:BUNDLE', $file2 );
	}
}
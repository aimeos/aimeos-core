<?php

namespace Aimeos\Controller\Jobs\Product\Export;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright \Aimeos\Aimeos (aimeos.org), 2015
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $context;
	private $aimeos;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );

		$this->context = \TestHelper::getContext();
		$this->aimeos = \TestHelper::getAimeos();

		$this->object = new \Aimeos\Controller\Jobs\Product\Export\Standard( $this->context, $this->aimeos );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		\Aimeos\MShop\Factory::setCache( false );
		\Aimeos\MShop\Factory::clear();

		$this->object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Product export', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Exports all available products';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$this->object->run();
		
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
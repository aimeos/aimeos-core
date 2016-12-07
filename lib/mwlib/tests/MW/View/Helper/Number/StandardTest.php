<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Number;


/**
 * Test class for \Aimeos\MW\View\Helper\Number.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Number\Standard( $view, '.', ' ', 3 );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( '1.000', $this->object->transform( 1 ) );
		$this->assertEquals( '1.000', $this->object->transform( 1.0 ) );
		$this->assertEquals( '1 000.000', $this->object->transform( 1000.0 ) );
	}


	public function testTransformNoDecimals()
	{
		$this->assertEquals( '1', $this->object->transform( 1, 0 ) );
		$this->assertEquals( '1', $this->object->transform( 1.0, 0 ) );
		$this->assertEquals( '1 000', $this->object->transform( 1000.0, 0 ) );
	}

}

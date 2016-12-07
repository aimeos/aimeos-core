<?php

namespace Aimeos\MW\View\Helper\Partial;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		$view = new \Aimeos\MW\View\Standard( array( __DIR__ => array( 'testfiles' ) ) );

		$this->object = new \Aimeos\MW\View\Helper\Partial\Standard( $view );
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
		$this->assertEquals( '', $this->object->transform( 'partial.html' ) );
	}


	public function testTransformParams()
	{
		$this->assertEquals( 'test', $this->object->transform( 'partial.html', array( 'testparam' => 'test' ) ) );
	}
}

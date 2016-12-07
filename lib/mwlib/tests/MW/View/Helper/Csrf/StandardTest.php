<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Csrf;


/**
 * Test class for \Aimeos\MW\View\Helper\Csrf\Standard.
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
		$this->object = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, 'cname', 'cvalue' );
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
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Helper\\Iface', $this->object->transform() );
	}


	public function testTransformName()
	{
		$this->assertEquals( 'cname', $this->object->transform()->name() );
	}


	public function testTransformValue()
	{
		$this->assertEquals( 'cvalue', $this->object->transform()->value() );
	}


	public function testTransformFormfield()
	{
		$expected = '<input class="csrf-token" type="hidden" name="cname" value="cvalue" />';

		$this->assertEquals( $expected, $this->object->transform()->formfield() );
	}


	public function testTransformFormfieldNone()
	{
		$view = new \Aimeos\MW\View\Standard();
		$object = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, 'cname', '' );

		$this->assertEquals( '', $object->transform()->formfield() );
	}
}

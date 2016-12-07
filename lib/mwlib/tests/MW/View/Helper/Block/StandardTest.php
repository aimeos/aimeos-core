<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MW\View\Helper\Block;


/**
 * Test class for \Aimeos\MW\View\Helper\Block\Standard.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Block\Standard( $view );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( '\\Aimeos\\MW\\View\\Helper\\Iface', $this->object->transform() );
	}


	public function testTransformGetSet()
	{
		$this->object->transform()->set( 'test', 'value' );
		$this->assertEquals( 'value', $this->object->transform()->get( 'test' ) );
	}


	public function testTransformStartStop()
	{
		$this->object->transform()->start( 'test' );
		echo 'value';
		$this->object->transform()->stop();

		$this->assertEquals( 'value', $this->object->transform()->get( 'test' ) );
	}
}

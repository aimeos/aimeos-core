<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 */


namespace Aimeos\MW\View\Helper\Value;


/**
 * Test class for \Aimeos\MW\View\Helper\Value.
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$param = array( 'key' => 'test', 'list' => array( 'test' => array( 'key' => 'value' ) ) );

		$view = new \Aimeos\MW\View\Standard();
		$helper = new \Aimeos\MW\View\Helper\Param\Standard( $view, $param );
		$view->addHelper( 'param', $helper );

		$this->object = new \Aimeos\MW\View\Helper\Value\Standard( $view );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertEquals( 'test', $this->object->transform( 'key', 'none' ) );
		$this->assertEquals( 'value', $this->object->transform( '/list/test/key', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( 'missing', 'none' ) );
	}


	public function testTransformNoDefault()
	{
		$this->assertEquals( 'test', $this->object->transform( 'key' ) );
		$this->assertEquals( 'value', $this->object->transform( '/list/test/key' ) );
		$this->assertEquals( null, $this->object->transform( 'missing' ) );
	}

}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
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
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Value\Standard( $view );
	}


	protected function tearDown()
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$params = array( 'key' => 'test', 'list' => array( 'test' => array( 'key' => 'value' ) ) );

		$this->assertEquals( 'test', $this->object->transform( $params, 'key', 'none' ) );
		$this->assertEquals( 'value', $this->object->transform( $params, '/list/test/key', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( $params, 'missing', 'none' ) );
	}


	public function testTransformNoDefault()
	{
		$params = array( 'key' => 'test', 'list' => array( 'test' => array( 'key' => 'value' ) ) );

		$this->assertEquals( 'test', $this->object->transform( $params, 'key' ) );
		$this->assertEquals( 'value', $this->object->transform( $params, '/list/test/key' ) );
		$this->assertEquals( null, $this->object->transform( $params, 'missing' ) );
	}
}

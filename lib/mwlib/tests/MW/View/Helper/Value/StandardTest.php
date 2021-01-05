<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Value;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$view->list = ['a' => 1, 'b' => 2, 'c' => 2];

		$this->object = new \Aimeos\MW\View\Helper\Value\Standard( $view );
	}


	protected function tearDown() : void
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


	public function testTransformString()
	{
		$this->assertEquals( 1, $this->object->transform( 'list', 'a', 'none' ) );
		$this->assertEquals( 'none', $this->object->transform( 'list', 'd', 'none' ) );
	}
}

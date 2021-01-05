<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\View\Helper\Csrf;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$view = new \Aimeos\MW\View\Standard();
		$this->object = new \Aimeos\MW\View\Helper\Csrf\Standard( $view, 'cname', 'cvalue' );
	}


	protected function tearDown() : void
	{
		$this->object = null;
	}


	public function testTransform()
	{
		$this->assertInstanceOf( \Aimeos\MW\View\Helper\Iface::class, $this->object->transform() );
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

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Session;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MW\Session\None();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testApply()
	{
		$result = $this->object->apply( ['test' => '123456789', 'test2' => '987654321'] );

		$this->assertInstanceOf( \Aimeos\MW\Session\Iface::class, $result );
		$this->assertEquals( '123456789', $this->object->get( 'test' ) );
		$this->assertEquals( '987654321', $this->object->get( 'test2' ) );
	}


	public function testDel()
	{
		$this->object->set( 'test', '123456789' );
		$this->assertEquals( '123456789', $this->object->get( 'test' ) );

		$result = $this->object->del( 'test' );

		$this->assertInstanceOf( \Aimeos\MW\Session\Iface::class, $result );
		$this->assertEquals( null, $this->object->get( 'test' ) );
	}


	public function testGet()
	{
		$this->assertEquals( null, $this->object->get( 'test' ) );

		$this->object->set( 'test', '123456789' );
		$this->assertEquals( '123456789', $this->object->get( 'test' ) );

		$this->object->set( 'test', ['123456789'] );
		$this->assertEquals( ['123456789'], $this->object->get( 'test' ) );
	}


	public function testPull()
	{
		$this->object->set( 'test', '123456789' );
		$this->assertEquals( '123456789', $this->object->get( 'test' ) );

		$this->assertEquals( '123456789', $this->object->pull( 'test' ) );
		$this->assertEquals( null, $this->object->pull( 'test' ) );
	}


	public function testRemove()
	{
		$this->object->set( 'test', '123456789' );
		$this->assertEquals( '123456789', $this->object->get( 'test' ) );

		$result = $this->object->remove( ['test'] );

		$this->assertInstanceOf( \Aimeos\MW\Session\Iface::class, $result );
		$this->assertEquals( null, $this->object->get( 'test' ) );
	}


	public function testSet()
	{
		$this->object->set( 'test', null );
		$this->assertEquals( null, $this->object->get( 'test' ) );

		$result = $this->object->set( 'test', '234' );

		$this->assertInstanceOf( \Aimeos\MW\Session\Iface::class, $result );
		$this->assertEquals( '234', $this->object->get( 'test' ) );
	}
}

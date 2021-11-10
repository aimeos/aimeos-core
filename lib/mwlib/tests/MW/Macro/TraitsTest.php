<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 */


namespace Aimeos\MW\Macro;


class TraitsTest extends \PHPUnit\Framework\TestCase
{
	public function testCall()
	{
		$this->assertEquals( 'B', ( new TestC() )->where() );
	}


	public function testProperty()
	{
		TestA::macro( 'test', function() {
			return $this->name;
		} );

		$this->assertEquals( 'A', ( new TestC() )->where() );
	}


	public function testMacroParent()
	{
		TestB::macro( 'test', function() {
			return 'B';
		} );

		$this->assertEquals( 'B', ( new TestC() )->where() );
	}


	public function testMacro()
	{
		TestC::macro( 'test', function() {
			return 'C';
		} );

		$this->assertEquals( 'C', ( new TestC() )->where() );
	}
}


class TestA implements Iface
{
	use Traits;

	private $name = 'A';
}


class TestB extends TestA
{
	protected function test( $arg )
	{
		return $arg;
	}
}


class TestC extends TestB
{
	public function where()
	{
		return $this->call( 'test', 'B' );
	}
}

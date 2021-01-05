<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Observer\Listener;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new TestListener();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MW\Observer\Listener\Iface::class, $this->object->register( new TestPublisher() ) );
	}


	public function testUpdate()
	{
		$this->assertEquals( 'value', $this->object->update( new TestPublisher(), 'test', 'value' ) );
	}
}


class TestListener implements \Aimeos\MW\Observer\Listener\Iface
{
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		return $this;
	}

	public function update( \Aimeos\MW\Observer\Publisher\Iface $p, string $action, $value = null )
	{
		return $value;
	}
}


class TestPublisher implements \Aimeos\MW\Observer\Publisher\Iface
{
	use \Aimeos\MW\Observer\Publisher\Traits;
}

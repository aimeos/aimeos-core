<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MW\Observer\Publisher;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new TestPublisher();
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testAttach()
	{
		$this->assertInstanceOf( \Aimeos\MW\Observer\Publisher\Iface::class, $this->object->attach( new TestListener(), 'test' ) );
	}

	public function testDetach()
	{
		$l = new TestListener();

		$this->object->attach( $l, 'test' );
		$this->assertInstanceOf( \Aimeos\MW\Observer\Publisher\Iface::class, $this->object->detach( $l, 'test' ) );
	}


	public function testOff()
	{
		$this->assertInstanceOf( \Aimeos\MW\Observer\Publisher\Iface::class, $this->object->off() );
	}


	public function testNotify()
	{
		$value = 'something';
		$l = new TestListener();

		$this->object->attach( $l, 'test' );
		$this->object->attach( $l, 'testagain' );

		$this->object->notifyPublic( 'test', $value );
		$this->assertEquals( 'something', $this->object->notifyPublic( 'testagain', $value ) );
	}
}


class TestPublisher implements \Aimeos\MW\Observer\Publisher\Iface
{
	use \Aimeos\MW\Observer\Publisher\Traits;

	/**
	 * @param string $action
	 * @param string|null $value
	 * @return mixed Modified value parameter
	 */
	public function notifyPublic( $action, $value = null )
	{
		return $this->notify( $action, $value );
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

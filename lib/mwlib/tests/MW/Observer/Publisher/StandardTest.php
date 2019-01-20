<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MW\Observer\Publisher;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new TestPublisher();
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testaddListener()
	{
		$this->object->addListener( new TestListener(), 'test' );
	}

	public function testRemoveListener()
	{
		$l = new TestListener();

		$this->object->addListener( $l, 'test' );
		$this->object->removeListener( $l, 'test' );
	}


	public function testclearListeners()
	{
		$this->object->clearListenersPublic();
	}


	public function testnotifyListeners()
	{
		$value = 'something';
		$l = new TestListener();

		$this->object->addListener( $l, 'test' );
		$this->object->addListener( $l, 'testagain' );

		$this->object->notifyListenersPublic( 'test', $value );
		$this->object->notifyListenersPublic( 'testagain', $value );
	}
}


class TestPublisher extends \Aimeos\MW\Observer\Publisher\Base
{
	/**
	 * @param string $action
	 * @param string|null $value
	 * @return mixed Modified value parameter
	 */
	public function notifyListenersPublic( $action, $value = null )
	{
		return $this->notifyListeners( $action, $value );
	}

	public function clearListenersPublic()
	{
		$this->clearListeners();
	}
}


class TestListener implements \Aimeos\MW\Observer\Listener\Iface
{
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
	}

	public function update( \Aimeos\MW\Observer\Publisher\Iface $p, $action, $value = null )
	{
		return $value;
	}
}

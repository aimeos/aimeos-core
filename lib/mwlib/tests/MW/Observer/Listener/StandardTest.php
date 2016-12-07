<?php

namespace Aimeos\MW\Observer\Listener;


/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$this->object = new TestListener;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
	}

	public function testRegister()
	{
		$p = new TestPublisher();

		$this->object->register($p);
	}

	public function testUpdate()
	{
		$p = new TestPublisher();

		$this->object->update($p, 'test');
	}
}


class TestListener implements \Aimeos\MW\Observer\Listener\Iface
{
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
	}

	public function update( \Aimeos\MW\Observer\Publisher\Iface $p, $action, $value = null )
	{
		if ($action == 'test') {
			return false;
		}
	}
}


class TestPublisher extends \Aimeos\MW\Observer\Publisher\Base
{
}

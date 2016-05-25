<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MShop\Order\Manager\Base;


class BaseTest extends \PHPUnit_Framework_TestCase
{
	private $object;


	protected function setUp()
	{
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testGetSetSession()
	{
		$order = $this->object->createItem();
		$order->setComment( 'test comment' );

		$this->object->setSession( $order, 'test' );
		$session = $this->object->getSession( 'test' );

		$this->assertInstanceof( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $session );
		$this->assertEquals( 'test comment', $order->getComment() );
		$this->assertEquals( $order, $session );
	}


	public function testGetSetSessionLock()
	{
		$lock = $this->object->getSessionLock( 'test' );
		$this->assertEquals( \Aimeos\MShop\Order\Manager\Base\Base::LOCK_DISABLE, $lock );

		$this->object->setSessionLock( \Aimeos\MShop\Order\Manager\Base\Base::LOCK_ENABLE, 'test' );

		$lock = $this->object->getSessionLock( 'test' );
		$this->assertEquals( \Aimeos\MShop\Order\Manager\Base\Base::LOCK_ENABLE, $lock );
	}
}

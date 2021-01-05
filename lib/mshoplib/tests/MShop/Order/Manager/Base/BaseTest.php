<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2021
 */


namespace Aimeos\MShop\Order\Manager\Base;


class BaseTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Order\Manager\Base\Standard( \TestHelperMShop::getContext() );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testGetSetSession()
	{
		$order = $this->object->create();
		$order->setComment( 'test comment' );

		$this->object->setSession( $order, 'test' );
		$session = $this->object->getSession( 'test' );

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Base\Iface::class, $session );
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

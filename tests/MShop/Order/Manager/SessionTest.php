<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2024
 */


namespace Aimeos\MShop\Order\Manager;


class SessionTest extends \PHPUnit\Framework\TestCase
{
	private $object;


	protected function setUp() : void
	{
		$this->object = new \Aimeos\MShop\Order\Manager\Standard( \TestHelper::context() );
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

		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Iface::class, $session );
		$this->assertEquals( 'test comment', $order->getComment() );
		$this->assertEquals( $order, $session );
	}
}

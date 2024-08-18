<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\MShop\Coupon\Provider;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$item = \Aimeos\MShop::create( $context, 'coupon' )->create();

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();
		$this->object = new \Aimeos\MShop\Coupon\Provider\None( $context, $item, '1234' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->order );
	}


	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->order ) );
	}
}

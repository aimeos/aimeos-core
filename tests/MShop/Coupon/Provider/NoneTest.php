<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 */


namespace Aimeos\MShop\Coupon\Provider;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$item = \Aimeos\MShop::create( $context, 'coupon' )->create();

		// Don't create order base item by create() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Standard( $priceManager->create(), $context->locale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\None( $context, $item, '1234' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );
	}
}

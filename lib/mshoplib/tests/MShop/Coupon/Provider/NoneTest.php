<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 */


namespace Aimeos\MShop\Coupon\Provider;


class NoneTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$item = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->createItem();

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
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

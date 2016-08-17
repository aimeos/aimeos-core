<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016
 */


namespace Aimeos\MShop\Coupon\Provider;


class NoneTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $orderBase;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$item = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $context )->createItem();

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\None( $context, $item, '1234' );
	}


	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testAddCoupon()
	{
		$this->object->addCoupon( $this->orderBase );
	}
}

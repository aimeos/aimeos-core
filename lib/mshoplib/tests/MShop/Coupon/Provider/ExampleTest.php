<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Coupon\Provider;


class ExampleTest extends \PHPUnit\Framework\TestCase
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
		$this->object = new \Aimeos\MShop\Coupon\Provider\Example( $context, $item, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->orderBase );
	}

	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}

	public function testSetObject()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->setObject( $this->object ) );
	}

	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );
	}
}

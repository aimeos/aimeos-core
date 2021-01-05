<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2021
 */

namespace Aimeos\MShop\Coupon\Provider\Decorator;


class ExampleTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$item = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->create();

		$this->orderBase = \Aimeos\MShop\Order\Manager\Factory::create( $context )
			->getSubmanager( 'base' )->create()->off();

		$provider = new \Aimeos\MShop\Coupon\Provider\Example( $context, $item, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Example( $provider, $context, $item, 'abcd' );
		$this->object->setObject( $this->object );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
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

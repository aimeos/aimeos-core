<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2020
 */


namespace Aimeos\MShop\Coupon\Provider\Decorator;


class SupplierTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $context;
	private $orderBase;
	private $couponItem;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( $this->context )->createItem();

		$provider = new \Aimeos\MShop\Coupon\Provider\Example( $this->context, $this->couponItem, 'abcd' );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Decorator\Supplier( $provider, $this->context, $this->couponItem, 'abcd' );
		$this->object->setObject( $this->object );

		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$serviceManager = \Aimeos\MShop::create( $this->context, 'service' );
		$service = $serviceManager->findItem( 'unitcode' );

		$orderServiceAttrManager = \Aimeos\MShop::create( $this->context, 'order/base/service/attribute' );
		$orderServiceAttr = $orderServiceAttrManager->createItem();
		$orderServiceAttr->setCode( 'supplier.code' );
		$orderServiceAttr->setType( 'delivery' );
		$orderServiceAttr->setValue( 'berlin' );

		$orderServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );
		$orderService = $orderServiceManager->createItem();
		$orderService->copyFrom( $service );
		$orderService->setAttributeItems( [$orderServiceAttr] );

		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $this->context->getLocale() );
		$this->orderBase->addService( $orderService, \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
		unset( $this->couponItem );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'supplier.code', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['supplier.code' => 'test'];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['supplier.code'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['supplier.code'] );
	}


	public function testIsAvailable()
	{
		$this->couponItem->setConfig( array( 'supplier.code' => 'berlin' ) );

		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableWrongSupplier()
	{
		$this->couponItem->setConfig( array( 'supplier.code' => 'hamburg' ) );

		$this->assertFalse( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testIsAvailableNoSupplier()
	{
		$this->assertFalse( $this->object->isAvailable( $this->orderBase ) );
	}
}

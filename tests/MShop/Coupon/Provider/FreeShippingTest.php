<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\MShop\Coupon\Provider;


class FreeShippingTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$couponItem = \Aimeos\MShop::create( $context, 'coupon' )->create();
		$couponItem->setConfig( array( 'freeshipping.productcode' => 'U:SD' ) );

		$this->object = new \Aimeos\MShop\Coupon\Provider\FreeShipping( $context, $couponItem, '90AB' );


		$delPrice = \Aimeos\MShop::create( $context, 'price' )->create();
		$delPrice->setCosts( '5.00' );
		$delPrice->setCurrencyId( 'EUR' );

		$manager = \Aimeos\MShop::create( $context, 'order/service' );

		$delivery = $manager->create();
		$delivery->setCode( 'test' );
		$delivery->setType( 'delivery' );
		$delivery->setPrice( $delPrice );

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();
		$this->order->addService( $delivery, 'delivery' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
	}


	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->order ) );
		$coupons = $this->order->getCoupons()->get( '90AB', [] );

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		// Test if service delivery item is available
		$this->order->getService( 'delivery' );

		$this->assertEquals( 1, count( $this->order->getProducts() ) );
		$this->assertEquals( '-5.00', $product->getPrice()->getCosts() );
		$this->assertEquals( '5.00', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:SD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getVendor() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Versandkosten Nachlass', $product->getName() );
	}


	public function testUpdateInvalidConfig()
	{
		$context = \TestHelper::context();

		$couponItem = \Aimeos\MShop::create( \TestHelper::context(), 'coupon' )->create();
		$object = new \Aimeos\MShop\Coupon\Provider\FreeShipping( $context, $couponItem, '90AB' );

		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$object->update( $this->order );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'freeshipping.productcode', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['freeshipping.productcode' => 'test'];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertNull( $result['freeshipping.productcode'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertIsString( $result['freeshipping.productcode'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}

}

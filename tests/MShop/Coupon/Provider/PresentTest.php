<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2024
 */


namespace Aimeos\MShop\Coupon\Provider;


class PresentTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$couponItem = \Aimeos\MShop::create( $context, 'coupon' )->create();
		$couponItem->setConfig( array( 'present.productcode' => 'U:PD', 'present.quantity' => '1' ) );

		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off();
		$this->object = new \Aimeos\MShop\Coupon\Provider\Present( $context, $couponItem, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->order );
	}


	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->order ) );

		$coupons = $this->order->getCoupons();
		$products = $this->order->getProducts();

		if( !isset( $coupons['90AB'][0] ) ) {
			throw new \RuntimeException( 'Missing coupon product' );
		}
		$product = $coupons['90AB'][0];

		$this->assertEquals( 1, count( $products ) );
		$this->assertEquals( 1, count( $coupons['90AB'] ) );
		$this->assertEquals( 'U:PD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getVendor() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geschenk Nachlass', $product->getName() );
	}


	public function testUpdateInvalidConfig()
	{
		$context = \TestHelper::context();
		$couponItem = \Aimeos\MShop::create( \TestHelper::context(), 'coupon' )->create();

		$object = new \Aimeos\MShop\Coupon\Provider\Present( $context, $couponItem, '90AB' );

		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$object->update( $this->order );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'present.productcode', $result );
		$this->assertArrayHasKey( 'present.quantity', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['present.productcode' => 'test', 'present.quantity' => '5'];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['present.productcode'] );
		$this->assertNull( $result['present.quantity'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['present.productcode'] );
		$this->assertNull( $result['present.quantity'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->order ) );
	}
}

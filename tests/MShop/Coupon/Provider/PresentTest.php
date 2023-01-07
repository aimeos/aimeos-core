<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2023
 */


namespace Aimeos\MShop\Coupon\Provider;


class PresentTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelper::context();

		$priceManager = \Aimeos\MShop::create( $context, 'price' );
		$couponItem = \Aimeos\MShop::create( $context, 'coupon' )->create();
		$couponItem->setConfig( array( 'present.productcode' => 'U:PD', 'present.quantity' => '1' ) );

		// Don't create order base item by create() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Standard( $priceManager->create(), $context->locale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Present( $context, $couponItem, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testUpdate()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons();
		$products = $this->orderBase->getProducts();

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
		$object->update( $this->orderBase );
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
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}
}

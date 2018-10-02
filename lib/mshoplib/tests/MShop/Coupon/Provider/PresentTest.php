<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Coupon\Provider;


class PresentTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $context )->createItem();
		$couponItem->setConfig( array( 'present.productcode' => 'U:PD', 'present.quantity' => '1' ) );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Present( $context, $couponItem, '90AB' );
	}


	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testAddCoupon()
	{
		$this->object->addCoupon( $this->orderBase );

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
		$this->assertEquals( '', $product->getSupplierCode() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geschenk Nachlass', $product->getName() );
	}


	public function testDeleteCoupon()
	{
		$this->object->addCoupon( $this->orderBase );
		$this->object->deleteCoupon( $this->orderBase );

		$coupons = $this->orderBase->getCoupons();
		$products = $this->orderBase->getProducts();

		$this->assertEquals( 0, count( $products ) );
		$this->assertArrayNotHasKey( '90AB', $coupons );
	}


	public function testAddCouponInvalidConfig()
	{
		$context = \TestHelperMShop::getContext();
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();

		$object = new \Aimeos\MShop\Coupon\Provider\Present( $context, $couponItem, '90AB' );

		$this->setExpectedException( '\\Aimeos\\MShop\\Coupon\\Exception' );
		$object->addCoupon( $this->orderBase );
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
		$this->assertInternalType( 'null', $result['present.productcode'] );
		$this->assertInternalType( 'null', $result['present.quantity'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertInternalType( 'string', $result['present.productcode'] );
		$this->assertInternalType( 'null', $result['present.quantity'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}
}

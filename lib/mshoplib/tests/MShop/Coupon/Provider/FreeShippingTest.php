<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2018
 */


namespace Aimeos\MShop\Coupon\Provider;


class FreeShippingTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();


		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $context )->createItem();
		$couponItem->setConfig( array( 'freeshipping.productcode' => 'U:SD' ) );

		$this->object = new \Aimeos\MShop\Coupon\Provider\FreeShipping( $context, $couponItem, '90AB' );


		$delPrice = \Aimeos\MShop\Price\Manager\Factory::createManager( $context )->createItem();
		$delPrice->setCosts( '5.00' );
		$delPrice->setCurrencyId( 'EUR' );

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$manager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context )
			->getSubManager( 'base' )->getSubManager( 'service' );

		$delivery = $manager->createItem();
		$delivery->setCode( 'test' );
		$delivery->setType( 'delivery' );
		$delivery->setPrice( $delPrice );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
		$this->orderBase->addService( $delivery, 'delivery' );
	}


	protected function tearDown()
	{
		unset( $this->object );
	}


	public function testAddCoupon()
	{
		$this->object->addCoupon( $this->orderBase );
		$coupons = $this->orderBase->getCoupons();

		if( ( $product = reset( $coupons['90AB'] ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		// Test if service delivery item is available
		$this->orderBase->getService( 'delivery' );

		$this->assertEquals( 1, count( $this->orderBase->getProducts() ) );
		$this->assertEquals( '-5.00', $product->getPrice()->getCosts() );
		$this->assertEquals( '5.00', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:SD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getSupplierCode() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Versandkosten Nachlass', $product->getName() );
	}


	public function testDeleteCoupon()
	{
		$this->object->addCoupon( $this->orderBase );
		$this->object->deleteCoupon( $this->orderBase );

		$products = $this->orderBase->getProducts();
		$coupons = $this->orderBase->getCoupons();

		$this->assertEquals( 0, count( $products ) );
		$this->assertArrayNotHasKey( '90AB', $coupons );
	}


	public function testAddCouponInvalidConfig()
	{
		$context = \TestHelperMShop::getContext();

		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();
		$object = new \Aimeos\MShop\Coupon\Provider\FreeShipping( $context, $couponItem, '90AB' );

		$this->setExpectedException( '\\Aimeos\\MShop\\Coupon\\Exception' );
		$object->addCoupon( $this->orderBase );
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
		$this->assertInternalType( 'null', $result['freeshipping.productcode'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 1, count( $result ) );
		$this->assertInternalType( 'string', $result['freeshipping.productcode'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}

}

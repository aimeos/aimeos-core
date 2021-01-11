<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2017-2021
 */


namespace Aimeos\MShop\Coupon\Provider;


class FixedRebateTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->create();
		$couponItem->setConfig( array( 'fixedrebate.productcode' => 'U:MD', 'fixedrebate.rebate' => ['EUR' => '2.50'] ) );

		// Don't create order base item by create() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->create(), $context->getLocale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\FixedRebate( $context, $couponItem, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'fixedrebate.productcode', $result );
		$this->assertArrayHasKey( 'fixedrebate.rebate', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = ['fixedrebate.productcode' => 'test', 'fixedrebate.rebate' => ['EUR' => '10.00']];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertNull( $result['fixedrebate.productcode'] );
		$this->assertNull( $result['fixedrebate.rebate'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 2, count( $result ) );
		$this->assertIsString( $result['fixedrebate.productcode'] );
		$this->assertIsString( $result['fixedrebate.rebate'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	public function testUpdate()
	{
		$products = $this->getOrderProducts();
		$this->orderBase->addProduct( $products['CNE'] );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );
		$products = $this->orderBase->getProducts();

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 2, count( $products ) );
		$this->assertEquals( '-2.50', $product->getPrice()->getValue() );
		$this->assertEquals( '2.50', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getSupplierName() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
	}


	public function testUpdateMultipleCurrencies()
	{
		$context = \TestHelperMShop::getContext();
		$config = array(
			'fixedrebate.productcode' => 'U:MD',
			'fixedrebate.rebate' => array(
				'EUR' => '1.25',
				'USD' => '1.50',
			),
		);

		$products = $this->getOrderProducts();
		$this->orderBase->addProduct( $products['CNE'] );

		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->create();
		$couponItem->setConfig( $config );

		$object = new \Aimeos\MShop\Coupon\Provider\FixedRebate( $context, $couponItem, '90AB' );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );
		$products = $this->orderBase->getProducts();

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 2, count( $products ) );
		$this->assertEquals( '-1.25', $product->getPrice()->getValue() );
		$this->assertEquals( '1.25', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
	}


	public function testUpdateMultipleTaxRates()
	{
		$products = $this->getOrderProducts();

		$products['CNC']->getPrice()->setTaxRate( '10.00' );
		$products['CNE']->getPrice()->setTaxRate( '20.00' );

		$products['CNC']->setQuantity( 1 );
		$products['CNE']->setQuantity( 1 );

		$this->orderBase->addProduct( $products['CNE'] );
		$this->orderBase->addProduct( $products['CNC'] );

		$context = \TestHelperMShop::getContext();
		$config = array(
			'fixedrebate.productcode' => 'U:MD',
			'fixedrebate.rebate' => array(
				'EUR' => '50.00',
			),
		);

		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->create();
		$couponItem->setConfig( $config );

		$object = new \Aimeos\MShop\Coupon\Provider\FixedRebate( $context, $couponItem, '90AB' );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );
		$products = $this->orderBase->getProducts();

		if( ( $couponProduct20 = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		if( ( $couponProduct10 = end( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 4, count( $products ) );
		$this->assertEquals( '-36.00', $couponProduct20->getPrice()->getValue() );
		$this->assertEquals( '-1.00', $couponProduct20->getPrice()->getCosts() );
		$this->assertEquals( '37.00', $couponProduct20->getPrice()->getRebate() );
		$this->assertEquals( '-13.00', $couponProduct10->getPrice()->getValue() );
		$this->assertEquals( 0, $couponProduct10->getPrice()->getCosts() );
		$this->assertEquals( '13.00', $couponProduct10->getPrice()->getRebate() );
	}


	public function testUpdateInvalidConfig()
	{
		$context = \TestHelperMShop::getContext();
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( \TestHelperMShop::getContext() )->create();
		$couponItem->setConfig( array( 'fixedrebate.rebate' => '2.50' ) );

		$object = new \Aimeos\MShop\Coupon\Provider\FixedRebate( $context, $couponItem, '90AB' );

		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$object->update( $this->orderBase );
	}


	protected function getOrderProducts()
	{
		$products = [];
		$manager = \Aimeos\MShop::create( \TestHelperMShop::getContext(), 'order/base/product' );

		$search = $manager->filter();
		$search->setConditions( $search->and( array(
			$search->compare( '==', 'order.base.product.prodcode', array( 'CNE', 'CNC' ) ),
			$search->compare( '==', 'order.base.product.price', array( '600.00', '36.00' ) )
		) ) );
		$items = $manager->search( $search )->toArray();

		if( count( $items ) < 2 ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		foreach( $items as $item ) {
			$products[$item->getProductCode()] = $item;
		}

		return $products;
	}
}

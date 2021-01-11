<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2020-2021
 */


namespace Aimeos\MShop\Coupon\Provider;


class TipTest extends \PHPUnit\Framework\TestCase
{
	private $coupon;
	private $object;
	private $orderBase;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::create( $context );
		$this->coupon = \Aimeos\MShop\Coupon\Manager\Factory::create( $context )->create();
		$this->coupon->setConfig( array( 'tip.productcode' => 'U:MD', 'tip.percent' => '10' ) );

		// Don't create order base item by create() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->create(), $context->getLocale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\Tip( $context, $this->coupon, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->orderBase );
	}


	public function testUpdate()
	{
		$orderProducts = $this->getOrderProducts();

		$this->orderBase->addProduct( $orderProducts['CNE'] );
		$this->orderBase->addProduct( $orderProducts['CNC'] );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );
		$products = $this->orderBase->getProducts();

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 3, count( $products ) );
		$this->assertEquals( 1, count( $coupons ) );
		$this->assertEquals( '67.20', $product->getPrice()->getValue() );
		$this->assertEquals( '0.00', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getSupplierName() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
	}


	public function testUpdateRoundUp()
	{
		$this->coupon->setConfig( [
			'tip.productcode' => 'U:MD', 'tip.percent' => '5.325',
			'tip.precision' => '2', 'tip.roundvalue' => '0.05'
		] );

		$orderProducts = $this->getOrderProducts();
		$this->orderBase->addProduct( $orderProducts['CNE'] );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 1, count( $coupons ) );
		$this->assertEquals( '3.85', $product->getPrice()->getValue() );
		$this->assertEquals( '0.00', $product->getPrice()->getRebate() );
	}


	public function testUpdateRoundDown()
	{
		$this->coupon->setConfig( [
			'tip.productcode' => 'U:MD', 'tip.percent' => '5.3',
			'tip.precision' => '2', 'tip.roundvalue' => '0.05'
		] );

		$orderProducts = $this->getOrderProducts();
		$this->orderBase->addProduct( $orderProducts['CNE'] );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $this->object->update( $this->orderBase ) );

		$coupons = $this->orderBase->getCoupons()->get( '90AB', [] );

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 1, count( $coupons ) );
		$this->assertEquals( '3.80', $product->getPrice()->getValue() );
		$this->assertEquals( '0.00', $product->getPrice()->getRebate() );
	}


	public function testUpdateInvalidConfig()
	{
		$context = \TestHelperMShop::getContext();
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::create( \TestHelperMShop::getContext() )->create();

		$object = new \Aimeos\MShop\Coupon\Provider\PercentRebate( $context, $couponItem, '90AB' );

		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$object->update( $this->orderBase );
	}


	public function testGetConfigBE()
	{
		$result = $this->object->getConfigBE();

		$this->assertArrayHasKey( 'tip.productcode', $result );
		$this->assertArrayHasKey( 'tip.percent', $result );
	}


	public function testCheckConfigBE()
	{
		$attributes = [
			'tip.productcode' => 'test', 'tip.percent' => '5',
			'tip.precision' => '2', 'tip.roundvalue' => '0.05'
		];
		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertNull( $result['tip.productcode'] );
		$this->assertNull( $result['tip.percent'] );
		$this->assertNull( $result['tip.precision'] );
		$this->assertNull( $result['tip.roundvalue'] );
	}


	public function testCheckConfigBEFailure()
	{
		$result = $this->object->checkConfigBE( [] );

		$this->assertEquals( 4, count( $result ) );
		$this->assertIsString( $result['tip.productcode'] );
		$this->assertIsString( $result['tip.percent'] );
	}


	public function testIsAvailable()
	{
		$this->assertTrue( $this->object->isAvailable( $this->orderBase ) );
	}


	/**
	 * Return the order products.
	 *
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface[]
	 * @throws \Exception
	 */
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

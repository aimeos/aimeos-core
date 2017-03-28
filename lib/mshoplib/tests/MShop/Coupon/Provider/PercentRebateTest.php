<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Coupon\Provider;


/**
 * Test class for \Aimeos\MShop\Coupon\Provider\PercentRebate.
 */
class PercentRebateTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $orderBase;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $context );
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $context )->createItem();
		$couponItem->setConfig( array( 'percentrebate.productcode' => 'U:MD', 'percentrebate.rebate' => '10' ) );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $context->getLocale() );
		$this->object = new \Aimeos\MShop\Coupon\Provider\PercentRebate( $context, $couponItem, 'zyxw' );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->object );
		unset( $this->orderBase );
	}


	public function testAddCoupon()
	{
		$orderProducts = $this->getOrderProducts();

		$this->orderBase->addProduct( $orderProducts['CNE'] );
		$this->orderBase->addProduct( $orderProducts['CNC'] );

		$this->object->addCoupon( $this->orderBase );

		$coupons = $this->orderBase->getCoupons();
		$products = $this->orderBase->getProducts();

		if( ( $product = reset( $coupons['zyxw'] ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 3, count( $products ) );
		$this->assertEquals( 1, count( $coupons['zyxw'] ) );
		$this->assertEquals( '-70.40', $product->getPrice()->getValue() );
		$this->assertEquals( '70.40', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getSupplierCode() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
	}


	public function testAddCouponMultipleTaxRates()
	{
		$products = $this->getOrderProducts();

		$products['CNC']->getPrice()->setTaxRate( '10.00' );
		$products['CNE']->getPrice()->setTaxRate( '20.00' );

		$products['CNC']->setQuantity( 1 );
		$products['CNE']->setQuantity( 1 );

		$this->orderBase->addProduct( $products['CNE'] );
		$this->orderBase->addProduct( $products['CNC'] );

		$this->object->addCoupon( $this->orderBase );

		$coupons = $this->orderBase->getCoupons();
		$products = $this->orderBase->getProducts();

		if( ( $couponProduct20 = reset( $coupons['zyxw'] ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		if( ( $couponProduct10 = end( $coupons['zyxw'] ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 4, count( $products ) );
		$this->assertEquals( '-37.00', $couponProduct20->getPrice()->getValue() );
		$this->assertEquals( '37.00', $couponProduct20->getPrice()->getRebate() );
		$this->assertEquals( '-29.70', $couponProduct10->getPrice()->getValue() );
		$this->assertEquals( '29.70', $couponProduct10->getPrice()->getRebate() );
	}


	public function testDeleteCoupon()
	{
		$orderProducts = $this->getOrderProducts();
		$this->orderBase->addProduct( $orderProducts['CNE'] );

		$this->object->addCoupon( $this->orderBase );
		$this->object->deleteCoupon( $this->orderBase );

		$products = $this->orderBase->getProducts();
		$coupons = $this->orderBase->getCoupons();

		$this->assertEquals( 1, count( $products ) );
		$this->assertArrayNotHasKey( 'zyxw', $coupons );
	}


	public function testAddCouponInvalidConfig()
	{
		$context = \TestHelperMShop::getContext();
		$couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( \TestHelperMShop::getContext() )->createItem();

		$object = new \Aimeos\MShop\Coupon\Provider\PercentRebate( $context, $couponItem, 'zyxw' );

		$this->setExpectedException( '\\Aimeos\\MShop\\Coupon\\Exception' );
		$object->addCoupon( $this->orderBase );
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
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperMShop::getContext(), 'order/base/product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->combine( '&&', array(
			$search->compare( '==', 'order.base.product.prodcode', array( 'CNE', 'CNC' ) ),
			$search->compare( '==', 'order.base.product.price', array( '600.00', '36.00' ) )
		) ) );
		$items = $manager->searchItems( $search );

		if( count( $items ) < 2 ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		foreach( $items as $item ) {
			$products[$item->getProductCode()] = $item;
		}

		return $products;
	}
}

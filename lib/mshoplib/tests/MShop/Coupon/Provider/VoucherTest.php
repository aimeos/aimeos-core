<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */


namespace Aimeos\MShop\Coupon\Provider;


class VoucherTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $couponItem;
	private $orderBase;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( $this->context );

		$this->couponItem = \Aimeos\MShop\Coupon\Manager\Factory::createManager( $this->context )->createItem();
		$this->couponItem->setConfig( array( 'voucher.productcode' => 'U:MD' ) );

		// Don't create order base item by createItem() as this would already register the plugins
		$this->orderBase = new \Aimeos\MShop\Order\Item\Base\Standard( $priceManager->createItem(), $this->context->getLocale() );

		$this->object = new \Aimeos\MShop\Coupon\Provider\Voucher( $this->context, $this->couponItem, '90AB' );
	}


	protected function tearDown()
	{
		unset( $this->context, $this->couponItem, $this->orderBase );
	}


	public function testAddCoupon()
	{
		$this->orderBase->addProduct( $this->getOrderProduct() );

		$orderProduct = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' )->createItem();
		$orderProduct->getPrice()->setCurrencyId( 'EUR' );
		$orderProduct->getPrice()->setValue( '100.00' );

		$object = $this->getMockBuilder( '\Aimeos\MShop\Coupon\Provider\Voucher' )
			->setConstructorArgs( [$this->context, $this->couponItem, '90AB'] )
			->setMethods( ['checkVoucher', 'getOrderProductItem', 'getUsedRebate'] )
			->getMock();

		$object->expects( $this->once() )->method( 'getOrderProductItem' )
			->will( $this->returnValue( $orderProduct ) );

		$object->expects( $this->once() )->method( 'getUsedRebate' )
			->will( $this->returnValue( 20.0 ) );

		$object->addCoupon( $this->orderBase );

		$coupons = $this->orderBase->getCoupons();
		$products = $this->orderBase->getProducts();

		if( ( $product = reset( $coupons['90AB'] ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 1, count( $coupons ) );
		$this->assertEquals( 2, count( $products ) );
		$this->assertEquals( '-74.00', $product->getPrice()->getValue() );
		$this->assertEquals( '74.00', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getSupplierCode() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
		$this->assertEquals( '6.00', $product->getAttribute( 'coupon-remain', 'coupon' ) );
	}


	public function testCheckVoucher()
	{
		$this->setExpectedException( '\Aimeos\MShop\Coupon\Exception' );
		$this->access( 'checkVoucher' )->invokeArgs( $this->object, [-1, [5,6]] );
	}


	public function testFilterOrderBaseIds()
	{
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.editor', 'core:unittest' ) );

		$list = [];
		foreach( $manager->searchItems( $search ) as $item ) {
			$list[] = $item->getBaseId();
		}
		sort( $list );

		$actual = $this->access( 'filterOrderBaseIds' )->invokeArgs( $this->object, [$list + [-1]] );
		sort( $actual );

		$this->assertEquals( $list, $actual );
	}


	public function testGetOrderProductItem()
	{
		$id = $this->getOrderProduct()->getId();

		$item = $this->access( 'getOrderProductItem' )->invokeArgs( $this->object, [$id, 'EUR'] );

		$this->assertEquals( $id, $item->getId() );

		$this->setExpectedException( '\Aimeos\MShop\Coupon\Exception' );
		$this->access( 'getOrderProductItem' )->invokeArgs( $this->object, [$id, 'XXX'] );
	}


	public function testGetUsedRebate()
	{
		$rebate = $this->access( 'getUsedRebate' )->invokeArgs( $this->object, ['5678'] );

		$this->assertEquals( 5.0, $rebate );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( '\Aimeos\MShop\Coupon\Provider\Voucher' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}


	protected function getOrderProduct()
	{
		$products = [];
		$manager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );

		$search = $manager->createSearch();
		$search->setConditions( $search->combine( '&&', array(
			$search->compare( '==', 'order.base.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.base.product.price', '36.00' )
		) ) );
		$items = $manager->searchItems( $search );

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		return $item;
	}
}

<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2025
 */


namespace Aimeos\MShop\Coupon\Provider;


class VoucherTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $couponItem;
	private $order;
	private $object;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();

		$this->couponItem = \Aimeos\MShop::create( $this->context, 'coupon' )->create();
		$this->couponItem->setConfig( array( 'voucher.productcode' => 'U:MD' ) );

		$this->order = \Aimeos\MShop::create( $this->context, 'order' )->create()->off();
		$this->object = new \Aimeos\MShop\Coupon\Provider\Voucher( $this->context, $this->couponItem, '90AB' );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->context, $this->couponItem, $this->order );
	}


	public function testUpdate()
	{
		$this->order->addProduct( $this->getOrderProduct() );

		$orderProduct = \Aimeos\MShop::create( $this->context, 'order/product' )->create();
		$orderProduct->getPrice()->setCurrencyId( 'EUR' );
		$orderProduct->getPrice()->setValue( '100.00' );

		$object = $this->getMockBuilder( \Aimeos\MShop\Coupon\Provider\Voucher::class )
			->setConstructorArgs( [$this->context, $this->couponItem, '90AB'] )
			->onlyMethods( ['checkVoucher', 'getOrderProductItem', 'getUsedRebate'] )
			->getMock();

		$object->expects( $this->once() )->method( 'getOrderProductItem' )
			->willReturn( $orderProduct );

		$object->expects( $this->once() )->method( 'getUsedRebate' )
			->willReturn( 20.0 );

		$this->assertInstanceOf( \Aimeos\MShop\Coupon\Provider\Iface::class, $object->update( $this->order ) );

		$coupons = $this->order->getCoupons()->get( '90AB', [] );
		$products = $this->order->getProducts();

		if( ( $product = reset( $coupons ) ) === false ) {
			throw new \RuntimeException( 'No coupon available' );
		}

		$this->assertEquals( 1, count( $coupons ) );
		$this->assertEquals( 2, count( $products ) );
		$this->assertEquals( '-72.00', $product->getPrice()->getValue() );
		$this->assertEquals( '-2.00', $product->getPrice()->getCosts() );
		$this->assertEquals( '74.00', $product->getPrice()->getRebate() );
		$this->assertEquals( 'U:MD', $product->getProductCode() );
		$this->assertNotEquals( '', $product->getProductId() );
		$this->assertEquals( '', $product->getVendor() );
		$this->assertEquals( '', $product->getMediaUrl() );
		$this->assertEquals( 'Geldwerter Nachlass', $product->getName() );
		$this->assertEquals( '6.00', $product->getAttribute( 'coupon-remain', 'coupon' ) );
	}


	public function testCheckVoucher()
	{
		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$this->access( 'checkVoucher' )->invokeArgs( $this->object, [-1, [5, 6]] );
	}


	public function testFilterOrderIds()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order' );
		$search = $manager->filter()->add( 'order.editor', '==', 'core' );
		$list = $manager->search( $search )->getId()->sort()->all();

		$actual = $this->access( 'filterOrderIds' )->invokeArgs( $this->object, [$list + [-1]] );
		sort( $actual );

		$this->assertEquals( $list, $actual );
	}


	public function testGetOrderProductItem()
	{
		$id = $this->getOrderProduct()->getId();

		$item = $this->access( 'getOrderProductItem' )->invokeArgs( $this->object, [$id, 'EUR'] );

		$this->assertEquals( $id, $item->getId() );

		$this->expectException( \Aimeos\MShop\Coupon\Exception::class );
		$this->access( 'getOrderProductItem' )->invokeArgs( $this->object, [$id, 'XXX'] );
	}


	public function testGetUsedRebate()
	{
		$rebate = $this->access( 'getUsedRebate' )->invokeArgs( $this->object, ['1234'] );

		$this->assertEquals( 5.0, $rebate );
	}


	protected function access( $name )
	{
		$class = new \ReflectionClass( \Aimeos\MShop\Coupon\Provider\Voucher::class );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );

		return $method;
	}


	protected function getOrderProduct()
	{
		$manager = \Aimeos\MShop::create( $this->context, 'order/product' );

		$search = $manager->filter();
		$search->setConditions( $search->and( array(
			$search->compare( '==', 'order.product.prodcode', 'CNE' ),
			$search->compare( '==', 'order.product.price', '36.00' )
		) ) );
		$items = $manager->search( $search )->toArray();

		if( ( $item = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'Please fix the test data in your database.' );
		}

		return $item;
	}
}

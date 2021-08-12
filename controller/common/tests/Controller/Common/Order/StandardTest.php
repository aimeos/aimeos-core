<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

namespace Aimeos\Controller\Common\Order;


class StandardTest extends \PHPUnit\Framework\TestCase
{
	protected function setUp() : void
	{
		\Aimeos\MShop::cache( true );
	}


	protected function tearDown() : void
	{
		\Aimeos\MShop::cache( false );
	}


	public function testBlock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStatus' ) )
			->getMock();

		$object->expects( $this->exactly( 2 ) )->method( 'updateStatus' )
			->with( $this->equalTo( $orderItem ), $this->anything(), $this->equalTo( 1 ), $this->equalTo( -1 ) );

		$object->block( $orderItem );
	}


	public function testUnblock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStatus' ) )
			->getMock();

		$object->expects( $this->exactly( 2 ) )->method( 'updateStatus' )
			->with( $this->equalTo( $orderItem ), $this->anything(), $this->equalTo( 0 ), $this->equalTo( +1 ) );

		$object->unblock( $orderItem );
	}


	public function testUpdateBlock()
	{
		$context = \TestHelperCntl::getContext();

		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();
		$orderItem->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'block' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'block' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testUpdateUnblock()
	{
		$context = \TestHelperCntl::getContext();

		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();
		$orderItem->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'unblock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'unblock' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testAddStatusItem()
	{
		$context = \TestHelperCntl::getContext();

		$statusStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Status\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$statusStub->expects( $this->once() )->method( 'save' );

		\Aimeos\MShop::inject( 'order/status', $statusStub );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'addStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( 1, 2, 3 ) );
	}


	public function testGetBundleMap()
	{
		$context = \TestHelperCntl::getContext();
		$prodId = \Aimeos\MShop::create( $context, 'product' )->find( 'CNC' )->getId();

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'getBundleMap' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( $prodId ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testGetContext()
	{
		$context = \TestHelperCntl::getContext();

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'getContext' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, [] );

		$this->assertInstanceOf( \Aimeos\MShop\Context\Item\Iface::class, $result );
		$this->assertSame( $context, $result );
	}


	public function testGetLastStatusItem()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = $this->getOrderItem( '2008-02-15 12:34:56' );

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( $orderItem->getId(), 'typestatus', 'shipped' ) );

		$this->assertNotEquals( false, $result );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Status\Iface::class, $result );
		$this->assertEquals( 'shipped', $result->getValue() );
	}


	public function testGetLastStatusItemFalse()
	{
		$context = \TestHelperCntl::getContext();

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( -1, 0, 0 ) );

		$this->assertNull( $result );
	}


	public function testGetStockItems()
	{
		$context = \TestHelperCntl::getContext();
		$prodid = \Aimeos\MShop::create( $context, 'product' )->find( 'CNE' )->getId();

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'getStockItems' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, [[$prodid], 'default'] );

		$this->assertEquals( 1, count( $result ) );

		foreach( $result as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $item );
		}
	}


	public function testUpdateCoupons()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderCouponStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Coupon\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderCouponStub->create()->setCode( 'test' )] ) ) );

		\Aimeos\MShop::inject( 'order/base/coupon', $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( \Aimeos\MShop\Coupon\Manager\Code\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop::inject( 'coupon/code', $couponCodeStub );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateCouponsException()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderCouponStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Coupon\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderCouponStub->create()->setCode( 'test' )] ) ) );

		\Aimeos\MShop::inject( 'order/base/coupon', $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( \Aimeos\MShop\Coupon\Manager\Code\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop::inject( 'coupon/code', $couponCodeStub );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );

		$this->expectException( \Exception::class );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStatus()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();
		$statusItem->setValue( 1 );

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'addStatusItem' );

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 'type', 1, 0 ) );
	}


	public function testUpdateStatusStock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateStock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$object->expects( $this->once() )->method( 'updateStock' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE, 1, 0 ) );
	}


	public function testUpdateStatusCoupons()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();

		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateCoupons' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$object->expects( $this->once() )->method( 'updateCoupons' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE, 1, 0 ) );
	}


	public function testUpdateStock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderProductStub->create()] ) ) );

		\Aimeos\MShop::inject( 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( 'stock', $stockStub );


		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockArticle()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderProductItem = $orderProductStub->create();
		$orderProductItem->setType( 'default' );

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderProductItem] ) ) );

		\Aimeos\MShop::inject( 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( 'stock', $stockStub );


		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockSelect()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderProductItem = $orderProductStub->create();
		$orderProductItem->setType( 'select' );

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->will( $this->returnValue( map( [$orderProductItem] ) ) );

		\Aimeos\MShop::inject( 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( 'stock', $stockStub );


		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->once() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockException()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Base\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( ['search'] )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop::inject( 'order/base/product', $orderProductStub );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );

		$this->expectException( \Exception::class );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockBundle()
	{
		$context = \TestHelperCntl::getContext();


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'save' )->with( $this->callback( function( $item ) {
			return $item->getStockLevel() === 10;
		} ) );

		\Aimeos\MShop::inject( 'stock', $stockStub );


		$stockItem = $stockStub->create();

		$stockItem1 = clone $stockItem;
		$stockItem1->setProductId( '123' );
		$stockItem1->setStockLevel( 10 );

		$stockItem2 = clone $stockItem;
		$stockItem2->setProductId( '456' );
		$stockItem2->setStockLevel( 20 );

		$stockItem3 = clone $stockItem;
		$stockItem3->setProductId( '789' );
		$stockItem3->setStockLevel( 30 );


		$object = $this->getMockBuilder( \Aimeos\Controller\Common\Order\Standard::class )
			->setConstructorArgs( [$context] )
			->setMethods( ['getBundleMap', 'getStockItems'] )
			->getMock();

		$object->expects( $this->once() )->method( 'getBundleMap' )
			->will( $this->returnValue( ['123' => ['789'], '456' => ['789']] ) );

		$object->expects( $this->exactly( 2 ) )->method( 'getStockItems' )
			->will( $this->onConsecutiveCalls(
				map( [$stockItem2, $stockItem1] ),
				map( [$stockItem3] )
			) );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStockBundle' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, [1, 'default'] );
	}


	public function testUpdateStockSelection()
	{
		$context = \TestHelperCntl::getContext();
		$prodId = \Aimeos\MShop::create( $context, 'product' )->find( 'U:TEST' )->getId();


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'save' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'save' )->with( $this->callback( function( $item ) {
			return $item->getStockLevel() === 300;
		} ) );

		\Aimeos\MShop::inject( 'stock', $stockStub );


		$class = new \ReflectionClass( \Aimeos\Controller\Common\Order\Standard::class );
		$method = $class->getMethod( 'updateStockSelection' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( $prodId, 'default' ) );
	}


	protected function getOrderItem( $datepayment ) : \Aimeos\MShop\Order\Item\Iface
	{
		$manager = \Aimeos\MShop::create( \TestHelperCntl::getContext(), 'order' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $datepayment ) );

		return $manager->search( $search )->first();
	}
}

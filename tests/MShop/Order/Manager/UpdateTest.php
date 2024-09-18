<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Order\Manager;


class UpdateTest extends \PHPUnit\Framework\TestCase
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
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'updateStatus' ) )
			->getMock();

		$object->expects( $this->exactly( 2 ) )->method( 'updateStatus' )
			->with( $this->equalTo( $orderItem ), $this->anything(), $this->equalTo( 1 ), $this->equalTo( -1 ) );

		$object->block( $orderItem );
	}


	public function testUnblock()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'updateStatus' ) )
			->getMock();

		$object->expects( $this->exactly( 2 ) )->method( 'updateStatus' )
			->with( $this->equalTo( $orderItem ), $this->anything(), $this->equalTo( 0 ), $this->equalTo( +1 ) );

		$object->unblock( $orderItem );
	}


	public function testUpdateBlock()
	{
		$context = \TestHelper::context();

		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();
		$orderItem->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'block' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'block' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testUpdateUnblock()
	{
		$context = \TestHelper::context();

		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();
		$orderItem->setStatusPayment( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'unblock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'unblock' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testAddStatusItem()
	{
		$context = \TestHelper::context();

		$statusStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Status\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'save' ) )
			->getMock();

		$statusStub->expects( $this->once() )->method( 'save' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Status\Standard::class, $statusStub );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'addStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$method->invokeArgs( $object, array( 1, 2, 3 ) );
	}


	public function testGetBundleMap()
	{
		$context = \TestHelper::context();
		$prodId = \Aimeos\MShop::create( $context, 'product' )->find( 'CNC' )->getId();

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'getBundleMap' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$result = $method->invokeArgs( $object, array( $prodId ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testGetContext()
	{
		$context = \TestHelper::context();

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'context' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$result = $method->invokeArgs( $object, [] );

		$this->assertInstanceOf( \Aimeos\MShop\ContextIface::class, $result );
		$this->assertSame( $context, $result );
	}


	public function testGetLastStatusItem()
	{
		$context = \TestHelper::context();
		$orderItem = $this->getOrderItem( '2008-02-15 12:34:56' );

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$result = $method->invokeArgs( $object, array( $orderItem->getId(), 'typestatus', 'shipped' ) );

		$this->assertNotEquals( false, $result );
		$this->assertInstanceOf( \Aimeos\MShop\Order\Item\Status\Iface::class, $result );
		$this->assertEquals( 'shipped', $result->getValue() );
	}


	public function testGetLastStatusItemFalse()
	{
		$context = \TestHelper::context();

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$result = $method->invokeArgs( $object, array( -1, 0, 0 ) );

		$this->assertNull( $result );
	}


	public function testGetStockItems()
	{
		$context = \TestHelper::context();
		$prodid = \Aimeos\MShop::create( $context, 'product' )->find( 'CNE' )->getId();

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'getStockItems' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$result = $method->invokeArgs( $object, [[$prodid], 'default'] );

		$this->assertEquals( 1, count( $result ) );

		foreach( $result as $item ) {
			$this->assertInstanceOf( \Aimeos\MShop\Stock\Item\Iface::class, $item );
		}
	}


	public function testUpdateCoupons()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderCouponStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Coupon\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$orderCouponStub->create()->setCode( 'test' )] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Coupon\Standard::class, $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( \Aimeos\MShop\Coupon\Manager\Code\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Coupon\Manager\Code\Standard::class, $couponCodeStub );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateCouponsException()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderCouponStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Coupon\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$orderCouponStub->create()->setCode( 'test' )] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Coupon\Standard::class, $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( \Aimeos\MShop\Coupon\Manager\Code\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Coupon\Manager\Code\Standard::class, $couponCodeStub );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );

		$this->expectException( \Exception::class );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStatus()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();
		$statusItem->setValue( 1 );

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'addStatusItem', 'getLastStatusItem' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'addStatusItem' );

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->willReturn( $statusItem );

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 'type', 1, 0 ) );
	}


	public function testUpdateStatusStock()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateStock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->willReturn( $statusItem );

		$object->expects( $this->once() )->method( 'updateStock' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE, 1, 0 ) );
	}


	public function testUpdateStatusCoupons()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create()->setId( -1 );
		$statusItem = \Aimeos\MShop::create( $context, 'order/status' )->create();

		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateCoupons' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->willReturn( $statusItem );

		$object->expects( $this->once() )->method( 'updateCoupons' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE, 1, 0 ) );
	}


	public function testUpdateStock()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$orderProductStub->create()] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Product\Standard::class, $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Stock\Manager\Standard::class, $stockStub );


		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockArticle()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderProductItem = $orderProductStub->create();
		$orderProductItem->setType( 'default' );

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$orderProductItem] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Product\Standard::class, $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Stock\Manager\Standard::class, $stockStub );


		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockSelect()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderProductItem = $orderProductStub->create();
		$orderProductItem->setType( 'select' );

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->willReturn( map( [$orderProductItem] ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Product\Standard::class, $orderProductStub );


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'decrease' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'decrease' );

		\Aimeos\MShop::inject( \Aimeos\MShop\Stock\Manager\Standard::class, $stockStub );


		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->once() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockException()
	{
		$context = \TestHelper::context();
		$orderItem = \Aimeos\MShop::create( $context, 'order' )->create();


		$orderProductStub = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Product\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( ['search'] )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'search' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Order\Manager\Product\Standard::class, $orderProductStub );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );

		$this->expectException( \Exception::class );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockBundle()
	{
		$context = \TestHelper::context();


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'save' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'save' )->with( $this->callback( function( $item ) {
			return $item->getStockLevel() === 10;
		} ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Stock\Manager\Standard::class, $stockStub );


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


		$object = $this->getMockBuilder( \Aimeos\MShop\Order\Manager\Standard::class )
			->setConstructorArgs( [$context] )
			->onlyMethods( ['getBundleMap', 'getStockItems'] )
			->getMock();

		$object->expects( $this->once() )->method( 'getBundleMap' )
			->willReturn( ['123' => ['789'], '456' => ['789']] );

		$object->expects( $this->exactly( 2 ) )->method( 'getStockItems' )
			->willReturn(
				map( [$stockItem2, $stockItem1] ),
				map( [$stockItem3] )
			);


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStockBundle' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, [1, 'default'] );
	}


	public function testUpdateStockSelection()
	{
		$context = \TestHelper::context();
		$prodId = \Aimeos\MShop::create( $context, 'product' )->find( 'U:TEST' )->getId();


		$stockStub = $this->getMockBuilder( \Aimeos\MShop\Stock\Manager\Standard::class )
			->setConstructorArgs( array( $context ) )
			->onlyMethods( array( 'save', 'type' ) )
			->getMock();

		$stockStub->method( 'type' )->willReturn( ['stock'] );

		$stockStub->expects( $this->once() )->method( 'save' )->with( $this->callback( function( $item ) {
			return $item->getStockLevel() === 300;
		} ) );

		\Aimeos\MShop::inject( \Aimeos\MShop\Stock\Manager\Standard::class, $stockStub );


		$class = new \ReflectionClass( \Aimeos\MShop\Order\Manager\Standard::class );
		$method = $class->getMethod( 'updateStockSelection' );
		$method->setAccessible( true );

		$object = new \Aimeos\MShop\Order\Manager\Standard( $context );
		$method->invokeArgs( $object, array( $prodId, 'default' ) );
	}


	protected function getOrderItem( $datepayment ) : \Aimeos\MShop\Order\Item\Iface
	{
		$manager = \Aimeos\MShop::create( \TestHelper::context(), 'order' );

		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $datepayment ) );

		return $manager->search( $search )->first();
	}
}

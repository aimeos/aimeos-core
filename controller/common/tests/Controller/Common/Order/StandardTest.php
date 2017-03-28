<?php

/**
 * @copyright Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\Controller\Common\Order;


class StandardTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
		\Aimeos\MShop\Factory::setCache( true );
	}


	protected function tearDown()
	{
		\Aimeos\MShop\Factory::clear();
		\Aimeos\MShop\Factory::setCache( false );
	}


	public function testBlock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
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
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
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

		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();
		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'block' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'block' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testUpdateUnblock()
	{
		$context = \TestHelperCntl::getContext();

		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();
		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'unblock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'unblock' )->with( $this->equalTo( $orderItem ) );

		$object->update( $orderItem );
	}


	public function testAddStatusItem()
	{
		$context = \TestHelperCntl::getContext();

		$statusStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Status\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$statusStub->expects( $this->once() )->method( 'saveItem' );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/status', $statusStub );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'addStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( 1, 2, 3 ) );
	}


	public function testGetBundleMap()
	{
		$context = \TestHelperCntl::getContext();
		$prodId = \Aimeos\MShop\Factory::createManager( $context, 'product' )->findItem( 'CNC' )->getId();

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'getBundleMap' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( $prodId ) );

		$this->assertEquals( 2, count( $result ) );
	}


	public function testGetContext()
	{
		$context = \TestHelperCntl::getContext();

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'getContext' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, [] );

		$this->assertInstanceOf( '\Aimeos\MShop\Context\Item\Iface', $result );
		$this->assertSame( $context, $result );
	}


	public function testGetLastStatusItem()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = $this->getOrderItem( '2008-02-15 12:34:56' );

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( $orderItem->getId(), 'typestatus' ) );

		$this->assertEquals( 1, count( $result ) );

		foreach( $result as $item )
		{
			$this->assertInstanceOf( '\Aimeos\MShop\Order\Item\Status\Iface', $item );
			$this->assertEquals( 'shipped', $item->getValue() );
		}
	}


	public function testGetLastStatusItemFalse()
	{
		$context = \TestHelperCntl::getContext();

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'getLastStatusItem' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( -1, 0 ) );

		$this->assertFalse( $result );
	}


	public function testGetStockItems()
	{
		$context = \TestHelperCntl::getContext();

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'getStockItems' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$result = $method->invokeArgs( $object, array( array( 'CNE' ), 'default' ) );

		$this->assertEquals( 1, count( $result ) );

		foreach( $result as $item ) {
			$this->assertInstanceOf( '\Aimeos\MShop\Stock\Item\Iface', $item );
		}
	}


	public function testUpdateCoupons()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderCouponStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Coupon\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderCouponStub->createItem() ) ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/coupon', $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( '\Aimeos\MShop\Coupon\Manager\Code\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop\Factory::injectManager( $context, 'coupon/code', $couponCodeStub );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateCouponsException()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderCouponStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Coupon\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderCouponStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderCouponStub->createItem() ) ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/coupon', $orderCouponStub );


		$couponCodeStub = $this->getMockBuilder( '\Aimeos\MShop\Coupon\Manager\Code\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$couponCodeStub->expects( $this->once() )->method( 'increase' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'coupon/code', $couponCodeStub );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateCoupons' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );

		$this->setExpectedException( '\Exception' );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStatus()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();
		$statusItem = \Aimeos\MShop\Factory::createManager( $context, 'order/status' )->createItem();
		$statusItem->setValue( 1 );

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'addStatusItem' );

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 'type', 1, 0 ) );
	}


	public function testUpdateStatusStock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();
		$statusItem = \Aimeos\MShop\Factory::createManager( $context, 'order/status' )->createItem();

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateStock' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$object->expects( $this->once() )->method( 'updateStock' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::STOCK_UPDATE, 1, 0 ) );
	}


	public function testUpdateStatusCoupons()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();
		$statusItem = \Aimeos\MShop\Factory::createManager( $context, 'order/status' )->createItem();

		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'addStatusItem', 'getLastStatusItem', 'updateCoupons' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getLastStatusItem' )
			->will( $this->returnValue( $statusItem ) );

		$object->expects( $this->once() )->method( 'updateCoupons' );
		$object->expects( $this->once() )->method( 'addStatusItem' );

		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStatus' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, \Aimeos\MShop\Order\Item\Status\Base::COUPON_UPDATE, 1, 0 ) );
	}


	public function testUpdateStock()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderProductStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Product\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderProductStub->createItem() ) ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( '\Aimeos\MShop\Stock\Manager\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop\Factory::injectManager( $context, 'stock', $stockStub );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockArticle()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderProductStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Product\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderProductItem = $orderProductStub->createItem();
		$orderProductItem->setType( 'default' );

		$orderProductStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderProductItem ) ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( '\Aimeos\MShop\Stock\Manager\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop\Factory::injectManager( $context, 'stock', $stockStub );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'updateStockBundle' );
		$object->expects( $this->never() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockSelect()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderProductStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Product\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderProductItem = $orderProductStub->createItem();
		$orderProductItem->setType( 'select' );

		$orderProductStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderProductItem ) ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/product', $orderProductStub );


		$stockStub = $this->getMockBuilder( '\Aimeos\MShop\Stock\Manager\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'increase' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'increase' );

		\Aimeos\MShop\Factory::injectManager( $context, 'stock', $stockStub );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'updateStockBundle', 'updateStockSelection' ) )
			->getMock();

		$object->expects( $this->never() )->method( 'updateStockBundle' );
		$object->expects( $this->once() )->method( 'updateStockSelection' );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockException()
	{
		$context = \TestHelperCntl::getContext();
		$orderItem = \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem();


		$orderProductStub = $this->getMockBuilder( '\Aimeos\MShop\Order\Manager\Base\Product\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'searchItems' ) )
			->getMock();

		$orderProductStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->throwException( new \RuntimeException() ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'order/base/product', $orderProductStub );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStock' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );

		$this->setExpectedException( '\Exception' );
		$method->invokeArgs( $object, array( $orderItem, 1 ) );
	}


	public function testUpdateStockBundle()
	{
		$context = \TestHelperCntl::getContext();


		$stockStub = $this->getMockBuilder( '\Aimeos\MShop\Stock\Manager\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'saveItem' )->with( $this->callback( function( $item ) {
			return $item->getStocklevel() === 10;
		} ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'stock', $stockStub );


		$stockItem = $stockStub->createItem();

		$stockItem1 = clone $stockItem;
		$stockItem1->setProductCode( 'X2' );
		$stockItem1->setStocklevel( 10 );

		$stockItem2 = clone $stockItem;
		$stockItem2->setProductCode( 'X3' );
		$stockItem2->setStocklevel( 20 );

		$stockItem3 = clone $stockItem;
		$stockItem3->setProductCode( 'X1' );
		$stockItem3->setStocklevel( 30 );


		$object = $this->getMockBuilder( '\Aimeos\Controller\Common\Order\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'getBundleMap', 'getStockItems' ) )
			->getMock();

		$object->expects( $this->once() )->method( 'getBundleMap' )
			->will( $this->returnValue( array( 'X2' => array( 'X1' ), 'X3' => array( 'X1' ) ) ) );

		$object->expects( $this->exactly( 2 ) )->method( 'getStockItems' )
			->will( $this->onConsecutiveCalls( array( $stockItem2, $stockItem1 ), array( $stockItem3 ) ) );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStockBundle' );
		$method->setAccessible( true );
		$method->invokeArgs( $object, array( 1, 'default' ) );
	}


	public function testUpdateStockSelection()
	{
		$context = \TestHelperCntl::getContext();
		$stockItem = \Aimeos\MShop\Factory::createManager( $context, 'stock' )->createItem();
		$prodId = \Aimeos\MShop\Factory::createManager( $context, 'product' )->findItem( 'U:TEST' )->getId();


		$stockStub = $this->getMockBuilder( '\Aimeos\MShop\Stock\Manager\Standard' )
			->setConstructorArgs( array( $context ) )
			->setMethods( array( 'saveItem' ) )
			->getMock();

		$stockStub->expects( $this->once() )->method( 'saveItem' )->with( $this->callback( function( $item ) {
			return $item->getStocklevel() === 300;
		} ) );

		\Aimeos\MShop\Factory::injectManager( $context, 'stock', $stockStub );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStockSelection' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( $prodId, 'default' ) );
	}


	protected function getOrderItem( $datepayment )
	{
		$manager = \Aimeos\MShop\Factory::createManager( \TestHelperCntl::getContext(), 'order' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.datepayment', $datepayment ) );

		$result = $manager->searchItems( $search );

		if( ( $item = reset( $result ) ) === false ) {
			throw new \RuntimeException( sprintf( 'No order item for payment date "%1$s" found', $datepayment ) );
		}

		return $item;
	}
}

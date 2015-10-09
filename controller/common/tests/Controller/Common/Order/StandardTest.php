<?php

namespace Aimeos\Controller\Common\Order;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testBlock()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerCommonOrderBlock';
		$context->getConfig()->set( 'mshop/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderStatusItem = $orderStatusManagerStub->createItem();
		$orderStatusItem->setValue( 1 );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $orderStatusManagerStub ) );

		$orderStatusManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderStatusItem ) ) );


		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$object->block( $orderItem );
	}


	public function testUnblock()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerCommonOrderUnblock';
		$context->getConfig()->set( 'mshop/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderStatusItem = $orderStatusManagerStub->createItem();
		$orderStatusItem->setValue( 0 );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $orderStatusManagerStub ) );

		$orderStatusManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderStatusItem ) ) );


		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$object->unblock( $orderItem );
	}


	public function testUpdate()
	{
		$context = \TestHelper::getContext();
		$config = $context->getConfig();

		$name = 'ControllerCommonOrderBlock';
		$config->set( 'mshop/order/manager/name', $name );
		$config->set( 'mshop/product/manager/name', $name );
		$config->set( 'mshop/coupon/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Status\\Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderProductManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Product\\Standard' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCouponManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Coupon\\Standard' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Standard' )
			->setMethods( array( 'getSubManager', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productStockManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Stock\\Standard' )
			->setMethods( array( 'increase' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponCodeManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Coupon\\Manager\\Code\\Standard' )
			->setMethods( array( 'increase' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );
		\Aimeos\MShop\Product\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Product\\Manager\\' . $name, $productManagerStub );
		\Aimeos\MShop\Coupon\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Coupon\\Manager\\' . $name, $couponManagerStub );


		$orderStatusItemBlocked = $orderStatusManagerStub->createItem();
		$orderStatusItemBlocked->setValue( 1 );

		$orderStatusItemUnblocked = $orderStatusManagerStub->createItem();
		$orderStatusItemUnblocked->setValue( 0 );


		$orderManagerStub->expects( $this->exactly( 12 ) )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls(
				$orderStatusManagerStub, $orderBaseManagerStub, $orderStatusManagerStub,
				$orderStatusManagerStub, $orderBaseManagerStub, $orderStatusManagerStub,
				$orderStatusManagerStub, $orderBaseManagerStub, $orderStatusManagerStub,
				$orderStatusManagerStub, $orderBaseManagerStub, $orderStatusManagerStub
			) );

		$orderBaseManagerStub->expects( $this->exactly( 4 ) )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls(
				$orderProductManagerStub, $orderCouponManagerStub,
				$orderProductManagerStub, $orderCouponManagerStub
			) );

		$orderStatusManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls(
				array( $orderStatusItemUnblocked ), array( $orderStatusItemUnblocked ),
				array( $orderStatusItemBlocked ), array( $orderStatusItemBlocked )
			) );

		$orderStatusManagerStub->expects( $this->exactly( 4 ) )->method( 'saveItem' );


		$orderProductManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderProductManagerStub->createItem() ) ) );

		$productManagerStub->expects( $this->exactly( 4 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $productStockManagerStub ) );

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array() ) );

		$productStockManagerStub->expects( $this->exactly( 2 ) )->method( 'increase' );


		$orderCouponManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderCouponManagerStub->createItem() ) ) );

		$couponManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $couponCodeManagerStub ) );

		$couponCodeManagerStub->expects( $this->exactly( 2 ) )->method( 'increase' );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_UNFINISHED );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$object->update( $orderItem );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_PENDING );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$object->update( $orderItem );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setPaymentStatus( \Aimeos\MShop\Order\Item\Base::PAY_DELETED );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$object->update( $orderItem );
	}


	public function testUpdateStockBundle()
	{
		$stockItems = array();
		$context = \TestHelper::getContext();
		$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:BUNDLE' ) );
		$bundleItems = $productManager->searchItems( $search, array( 'product' ) );


		$name = 'ControllerCommonOrderUpdate';
		$context->getConfig()->set( 'mshop/product/manager/name', $name );

		$stockManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Stock\\Standard' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Product\\Manager\\Standard' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $stockManagerStub ) );

		\Aimeos\MShop\Product\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Product\\Manager\\' . $name, $productManagerStub );


		$stock = 10;

		foreach( $bundleItems as $bundleId => $bundleItem )
		{
			foreach( $bundleItem->getRefItems( 'product', null, 'default' ) as $refItem )
			{
				$stockItem = $stockManagerStub->createItem();
				$stockItem->setProductId( $refItem->getId() );
				$stockItem->setStockLevel( $stock );

				$stockItems[] = $stockItem;
				$stock += 10;
			}

			$bundleStockItem = $stockManagerStub->createItem();
			$bundleStockItem->setProductId( $bundleId );
			$bundleStockItem->setStockLevel( $stock - 5 );
		}

		$fcn = function( $subject ) {
			return ( $subject->getStockLevel() === 10 );
		};

		$stockManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( $stockItems, array( $bundleStockItem ) ) );

		$stockManagerStub->expects( $this->exactly( 1 ) )->method( 'saveItem' )
			->with( $this->callback( $fcn ) );


		$class = new \ReflectionClass( '\\Aimeos\\Controller\\Common\\Order\\Standard' );
		$method = $class->getMethod( 'updateStockBundle' );
		$method->setAccessible( true );

		$object = new \Aimeos\Controller\Common\Order\Standard( $context );
		$method->invokeArgs( $object, array( $bundleItems, 'default' ) );
	}
}

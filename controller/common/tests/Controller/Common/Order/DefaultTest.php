<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Common_Order_DefaultTest extends PHPUnit_Framework_TestCase
{
	public function testBlock()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerCommonOrderBlock';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Status_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderStatusItem = $orderStatusManagerStub->createItem();
		$orderStatusItem->setValue( 1 );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $orderStatusManagerStub ) );

		$orderStatusManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderStatusItem ) ) );


		$object = new Controller_Common_Order_Default( $context );
		$object->block( $orderItem );
	}


	public function testUnblock()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerCommonOrderUnblock';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Status_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderStatusItem = $orderStatusManagerStub->createItem();
		$orderStatusItem->setValue( 0 );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $orderStatusManagerStub ) );

		$orderStatusManagerStub->expects( $this->exactly( 2 ) )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderStatusItem ) ) );


		$object = new Controller_Common_Order_Default( $context );
		$object->unblock( $orderItem );
	}


	public function testUpdate()
	{
		$context = TestHelper::getContext();
		$config = $context->getConfig();

		$name = 'ControllerCommonOrderBlock';
		$config->set( 'classes/order/manager/name', $name );
		$config->set( 'classes/product/manager/name', $name );
		$config->set( 'classes/coupon/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Status_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderProductManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Product_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCouponManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Coupon_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub = $this->getMockBuilder( 'MShop_Product_Manager_Default' )
			->setMethods( array( 'getSubManager', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productStockManagerStub = $this->getMockBuilder( 'MShop_Product_Manager_Stock_Default' )
			->setMethods( array( 'increase' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponCodeManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Code_Default' )
			->setMethods( array( 'increase' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );
		MShop_Product_Manager_Factory::injectManager( 'MShop_Product_Manager_' . $name, $productManagerStub );
		MShop_Coupon_Manager_Factory::injectManager( 'MShop_Coupon_Manager_' . $name, $couponManagerStub );


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
		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_UNFINISHED );

		$object = new Controller_Common_Order_Default( $context );
		$object->update( $orderItem );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_PENDING );

		$object = new Controller_Common_Order_Default( $context );
		$object->update( $orderItem );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setPaymentStatus( MShop_Order_Item_Abstract::PAY_DELETED );

		$object = new Controller_Common_Order_Default( $context );
		$object->update( $orderItem );
	}


	public function testUpdateStockBundle()
	{
		$stockItems = array();
		$context = TestHelper::getContext();
		$productManager = MShop_Factory::createManager( $context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'U:BUNDLE' ) );
		$bundleItems = $productManager->searchItems( $search, array( 'product' ) );


		$name = 'ControllerCommonOrderUpdate';
		$context->getConfig()->set( 'classes/product/manager/name', $name );

		$stockManagerStub = $this->getMockBuilder( 'MShop_Product_Manager_Stock_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub = $this->getMockBuilder( 'MShop_Product_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$productManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $stockManagerStub ) );

		MShop_Product_Manager_Factory::injectManager( 'MShop_Product_Manager_' . $name, $productManagerStub );


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


		$class = new ReflectionClass( 'Controller_Common_Order_Default' );
		$method = $class->getMethod( '_updateStockBundle' );
		$method->setAccessible( true );

		$object = new Controller_Common_Order_Default( $context );
		$method->invokeArgs( $object, array( $bundleItems, 'default' ) );
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Controller_Common_Order_DefaultTest extends MW_Unittest_Testcase
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
			->setMethods( array( 'getSubManager' ) )
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
		MShop_Order_Manager_Factory::injectManager( 'MShop_Product_Manager_' . $name, $productManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Coupon_Manager_' . $name, $couponManagerStub );


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

		$productManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->returnValue( $productStockManagerStub ) );

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
}

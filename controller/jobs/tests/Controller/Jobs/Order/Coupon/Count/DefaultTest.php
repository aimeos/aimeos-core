<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Order_Coupon_Count_DefaultTest
	extends MW_Unittest_Testcase
{
	private $_object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();

		$this->_object = new Controller_Jobs_Order_Coupon_Count_Default( $context, $arcavias );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->_object = null;
		MShop_Factory::clear();
	}


	public function testGetName()
	{
		$this->assertEquals( 'Order coupon counts', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Decreases the counts of successfully redeemed coupons';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsOrderCouponCountDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );
		$context->getConfig()->set( 'classes/coupon/manager/name', $name );


		$couponManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponCodeManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Code_Default' )
			->setMethods( array( 'decrease' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'searchItems', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Status_Default' )
			->setMethods( array( 'saveItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCouponManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Coupon_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Coupon_Manager_' . $name, $couponManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setBaseId( 1 );
		$orderItem->setId( 2 );

		$orderCouponItem = $orderCouponManagerStub->createItem();
		$orderCouponItem->setBaseId( 1 );


		$couponManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $couponCodeManagerStub ) );

		$orderManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderCouponManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem->getId() => $orderItem ), array() ) );

		$orderCouponManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderCouponItem ) ) );

		$couponCodeManagerStub->expects( $this->once() )->method( 'decrease' );

		$orderStatusManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Coupon_Count_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunException()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsOrderCouponCountDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );
		$context->getConfig()->set( 'classes/coupon/manager/name', $name );


		$couponManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$couponCodeManagerStub = $this->getMockBuilder( 'MShop_Coupon_Manager_Code_Default' )
			->setMethods( array( 'decrease' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'searchItems', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderStatusManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Status_Default' )
			->setMethods( array( 'saveItem' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setMethods( array( 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCouponManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Coupon_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Coupon_Manager_' . $name, $couponManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setBaseId( 1 );
		$orderItem->setId( 2 );

		$orderCouponItem = $orderCouponManagerStub->createItem();
		$orderCouponItem->setBaseId( 1 );


		$couponManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->returnValue( $couponCodeManagerStub ) );

		$orderManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderCouponManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem->getId() => $orderItem ), array() ) );

		$orderCouponManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderCouponItem ), array() ) );

		$couponCodeManagerStub->expects( $this->once() )->method( 'decrease' )
			->will( $this->throwException( new MShop_Coupon_Exception( 'test order coupon exception' ) ) );

		$orderStatusManagerStub->expects( $this->never() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Coupon_Count_Default( $context, $arcavias );
		$object->run();
	}
}

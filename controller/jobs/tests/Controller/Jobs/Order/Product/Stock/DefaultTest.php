<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Order_Product_Stock_DefaultTest extends MW_Unittest_Testcase
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

		$this->_object = new Controller_Jobs_Order_Product_Stock_Default( $context, $arcavias );
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
	}


	public function testGetName()
	{
		$this->assertEquals( 'Order product stock levels', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Decreases the stock levels of products in completed orders';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsOrderStockDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


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

		$orderProductManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Product_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderProductItem = $orderProductManagerStub->createItem();


		$orderManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderProductManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );

		$orderProductManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderProductItem ) ) );

		$orderStatusManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Product_Stock_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunException()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsOrderStockDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


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

		$orderProductManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Product_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderProductItem = $orderProductManagerStub->createItem();


		$orderManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderProductManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );

		$orderProductManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->throwException( new MShop_Order_Exception( 'test order stock exception' ) ) );

		$orderStatusManagerStub->expects( $this->never() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Product_Stock_Default( $context, $arcavias );
		$object->run();
	}
}

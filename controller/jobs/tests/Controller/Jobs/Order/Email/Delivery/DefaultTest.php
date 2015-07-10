<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Order_Email_Delivery_DefaultTest extends MW_Unittest_Testcase
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

		$this->_object = new Controller_Jobs_Order_Email_Delivery_Default( $context, $arcavias );
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
		$this->assertEquals( 'Order delivery related e-mails', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends order delivery status update e-mails';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$mailStub = $this->getMockBuilder( 'MW_Mail_None' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( 'MW_Mail_Message_None' )
			->disableOriginalConstructor()
			->disableOriginalClone()
			->getMock();

		$mailStub->expects( $this->once() )
			->method( 'createMessage' )
			->will( $this->returnValue( $mailMsgStub ) );

		$mailStub->expects( $this->once() )->method( 'send' );

		$context->setMail( $mailStub );


		$orderAddressItem = MShop_Order_Manager_Factory::createManager( $context )
			->getSubManager( 'base' )->getSubManager( 'address' )->createItem();


		$name = 'ControllerJobsEmailDeliveryDefaultRun';
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
			->setMethods( array( 'load' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = new MShop_Order_Item_Default( array( 'ctime' => '2000-01-01 00:00:00' ) );
		$orderBaseItem = $orderBaseManagerStub->createItem();
		$orderBaseItem->setAddress( $orderAddressItem );


		$orderManagerStub->expects( $this->exactly( 2 ) )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array(), array(), array() ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( $orderBaseItem ) );

		$orderStatusManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Email_Delivery_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunException()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$mailStub = $this->getMockBuilder( 'MW_Mail_None' )
			->disableOriginalConstructor()
			->getMock();

		$context->setMail( $mailStub );


		$name = 'ControllerJobsEmailDeliveryDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();


		$orderManagerStub->expects( $this->exactly( 4 ) )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array(), array(), array() ) );


		$object = new Controller_Jobs_Order_Email_Delivery_Default( $context, $arcavias );
		$object->run();
	}

}

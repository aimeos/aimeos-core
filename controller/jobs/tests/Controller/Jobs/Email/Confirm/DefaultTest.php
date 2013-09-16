<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Email_Confirm_DefaultTest extends MW_Unittest_Testcase
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

		$this->_object = new Controller_Jobs_Email_Confirm_Default( $context, $arcavias );
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
		$this->assertEquals( 'Confirmation e-mails', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends a confirmation e-mail to the customer for each completed order';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$mailStub = $this->getMockBuilder( 'MW_Mail_Zend' )
			->disableOriginalConstructor()
			->getMock();

		$mailMsgStub = $this->getMockBuilder( 'MW_Mail_Message_Zend' )
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


		$name = 'ControllerJobsEmailConfirmDefaultRun';
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


		$orderManagerStub->expects( $this->atLeastOnce() )->method( 'getSubManager' )
			->will( $this->onConsecutiveCalls( $orderStatusManagerStub, $orderBaseManagerStub ) );

		$orderItem = $orderManagerStub->createItem();
		$orderItem->setId( -1 );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );

		$orderBaseItem = $orderBaseManagerStub->createItem();
		$orderBaseItem->setAddress( $orderAddressItem );

		$orderBaseManagerStub->expects( $this->once() )->method( 'load' )
			->will( $this->returnValue( $orderBaseItem ) );

		$orderStatusManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Jobs_Email_Confirm_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunException()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$mailStub = $this->getMockBuilder( 'MW_Mail_Zend' )
			->disableOriginalConstructor()
			->getMock();

		$context->setMail( $mailStub );


		$name = 'ControllerJobsEmailConfirmDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );

		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setId( -1 );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );


		$object = new Controller_Jobs_Email_Confirm_Default( $context, $arcavias );
		$object->run();
	}

}

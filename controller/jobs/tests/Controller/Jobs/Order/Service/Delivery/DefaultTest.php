<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Order_Service_Delivery_DefaultTest extends PHPUnit_Framework_TestCase
{
	private $object;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();

		$this->object = new Controller_Jobs_Order_Service_Delivery_Default( $context, $aimeos );
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		$this->object = null;
	}


	public function testGetName()
	{
		$this->assertEquals( 'Process order delivery services', $this->object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Sends paid orders to the ERP system or logistic partner';
		$this->assertEquals( $text, $this->object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();


		$name = 'ControllerJobsServiceDeliveryProcessDefaultRun';
		$context->getConfig()->set( 'classes/service/manager/name', $name );
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$serviceManagerStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_' . $name, $serviceManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$serviceItem = $serviceManagerStub->createItem();
		$orderItem = $orderManagerStub->createItem();

		$serviceProviderStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();


		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->returnValue( $serviceProviderStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );

		$serviceProviderStub->expects( $this->once() )->method( 'process' );

		$orderManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Service_Delivery_Default( $context, $aimeos );
		$object->run();
	}


	public function testRunExceptionProcess()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();


		$name = 'ControllerJobsServiceDeliveryProcessDefaultRun';
		$context->getConfig()->set( 'classes/service/manager/name', $name );
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$serviceManagerStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_' . $name, $serviceManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$serviceItem = $serviceManagerStub->createItem();
		$orderItem = $orderManagerStub->createItem();

		$serviceProviderStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();


		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->returnValue( $serviceProviderStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $orderItem ), array() ) );

		$serviceProviderStub->expects( $this->once() )->method( 'process' )
			->will( $this->throwException( new MShop_Service_Exception( 'test order service delivery: process' ) ) );

		$orderManagerStub->expects( $this->never() )->method( 'saveItem' );


		$object = new Controller_Jobs_Order_Service_Delivery_Default( $context, $aimeos );
		$object->run();
	}


	public function testRunExceptionProvider()
	{
		$context = TestHelper::getContext();
		$aimeos = TestHelper::getAimeos();


		$name = 'ControllerJobsServiceDeliveryProcessDefaultRun';
		$context->getConfig()->set( 'classes/service/manager/name', $name );
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'saveItem', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$serviceManagerStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_' . $name, $serviceManagerStub );
		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$serviceItem = $serviceManagerStub->createItem();

		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->throwException( new MShop_Service_Exception( 'test sorder service delivery: getProvider' ) ) );

		$orderManagerStub->expects( $this->never() )->method( 'searchItems' );


		$object = new Controller_Jobs_Order_Service_Delivery_Default( $context, $aimeos );
		$object->run();
	}
}

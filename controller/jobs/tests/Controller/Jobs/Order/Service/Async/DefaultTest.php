<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


class Controller_Jobs_Order_Service_Async_DefaultTest extends MW_Unittest_Testcase
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

		$this->_object = new Controller_Jobs_Order_Service_Async_Default( $context, $arcavias );
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
		$this->assertEquals( 'Batch update of payment/delivery status', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Executes payment or delivery service providers that uses batch updates';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsServiceAsyncProcessDefaultRun';
		$context->getConfig()->set( 'classes/service/manager/name', $name );


		$serviceManagerStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_' . $name, $serviceManagerStub );


		$serviceItem = $serviceManagerStub->createItem();

		$serviceProviderStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceItem ) )
			->getMock();


		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->returnValue( $serviceProviderStub ) );

		$serviceProviderStub->expects( $this->once() )->method( 'updateAsync' );


		$object = new Controller_Jobs_Order_Service_Async_Default( $context, $arcavias );
		$object->run();
	}


	public function testRunExceptionProcess()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsServiceAsyncProcessDefaultRun';
		$context->getConfig()->set( 'classes/service/manager/name', $name );


		$serviceManagerStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setMethods( array( 'getProvider', 'searchItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_' . $name, $serviceManagerStub );


		$serviceItem = $serviceManagerStub->createItem();

		$serviceManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->onConsecutiveCalls( array( $serviceItem ), array() ) );

		$serviceManagerStub->expects( $this->once() )->method( 'getProvider' )
			->will( $this->throwException( new MShop_Service_Exception() ) );


		$object = new Controller_Jobs_Order_Service_Async_Default( $context, $arcavias );
		$object->run();
	}
}

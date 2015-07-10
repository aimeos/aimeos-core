<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */


class Controller_Jobs_Order_Cleanup_Unfinished_DefaultTest
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

		$this->_object = new Controller_Jobs_Order_Cleanup_Unfinished_Default( $context, $arcavias );
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
		$this->assertEquals( 'Removes unfinished orders', $this->_object->getName() );
	}


	public function testGetDescription()
	{
		$text = 'Deletes unfinished orders an makes their products and coupon codes available again';
		$this->assertEquals( $text, $this->_object->getDescription() );
	}


	public function testRun()
	{
		$context = TestHelper::getContext();
		$arcavias = TestHelper::getArcavias();


		$name = 'ControllerJobsOrderCleanupUnfinishedDefaultRun';
		$context->getConfig()->set( 'classes/order/manager/name', $name );
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'searchItems', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
			->setMethods( array( 'deleteItems' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderCntlStub = $this->getMockBuilder( 'Controller_Common_Order_Default' )
			->setMethods( array( 'unblock' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();


		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );
		Controller_Common_Order_Factory::injectController( 'Controller_Common_Order_' . $name, $orderCntlStub );


		$orderItem = $orderManagerStub->createItem();
		$orderItem->setBaseId( 1 );
		$orderItem->setId( 2 );


		$orderManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $orderItem->getId() => $orderItem ) ) );

		$orderBaseManagerStub->expects( $this->once() )->method( 'deleteItems' );

		$orderCntlStub->expects( $this->once() )->method( 'unblock' );


		$object = new Controller_Jobs_Order_Cleanup_Unfinished_Default( $context, $arcavias );
		$object->run();
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class Controller_Frontend_Order_StandardTest extends PHPUnit_Framework_TestCase
{
	public function testStore()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerFrontendOrderStore';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Standard' )
			->setMethods( array( 'saveItem', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Standard' )
			->setMethods( array( 'store' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		MShop_Order_Manager_Factory::injectManager( 'MShop_Order_Manager_' . $name, $orderManagerStub );


		$orderBaseItem = $orderBaseManagerStub->createItem();
		$orderBaseItem->setId( 1 );


		$orderBaseManagerStub->expects( $this->once() )->method( 'store' );

		$orderManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new Controller_Frontend_Order_Standard( $context );
		$object->store( $orderBaseItem );
	}


	public function testBlock()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerFrontendOrderBlock';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( 'Controller_Common_Order_Standard' )
			->setMethods( array( 'block' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		Controller_Common_Order_Factory::injectController( 'Controller_Common_Order_' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'block' );


		$object = new Controller_Frontend_Order_Standard( $context );
		$object->block( MShop_Factory::createManager( $context, 'order' )->createItem() );
	}


	public function testUnblock()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerFrontendOrderUnblock';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( 'Controller_Common_Order_Standard' )
			->setMethods( array( 'unblock' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		Controller_Common_Order_Factory::injectController( 'Controller_Common_Order_' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'unblock' );


		$object = new Controller_Frontend_Order_Standard( $context );
		$object->unblock( MShop_Factory::createManager( $context, 'order' )->createItem() );
	}


	public function testUpdate()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerFrontendOrderUpdate';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( 'Controller_Common_Order_Standard' )
			->setMethods( array( 'update' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		Controller_Common_Order_Factory::injectController( 'Controller_Common_Order_' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'update' );


		$object = new Controller_Frontend_Order_Standard( $context );
		$object->update( MShop_Factory::createManager( $context, 'order' )->createItem() );
	}
}

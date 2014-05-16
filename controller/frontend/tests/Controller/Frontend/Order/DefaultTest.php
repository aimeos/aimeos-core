<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

class Controller_Frontend_Order_DefaultTest extends MW_Unittest_Testcase
{
	public function testStore()
	{
		$context = TestHelper::getContext();
		$name = 'ControllerFrontendOrderStore';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Default' )
			->setMethods( array( 'saveItem', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( 'MShop_Order_Manager_Base_Default' )
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


		$object = new Controller_Frontend_Order_Default( $context );
		$object->store( $orderBaseItem );
	}
}

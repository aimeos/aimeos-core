<?php

namespace Aimeos\Controller\Frontend\Order;


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */
class StandardTest extends \PHPUnit_Framework_TestCase
{
	public function testStore()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerFrontendOrderStore';
		$context->getConfig()->set( 'classes/order/manager/name', $name );


		$orderManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setMethods( array( 'saveItem', 'getSubManager' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		$orderBaseManagerStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setMethods( array( 'store' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\' . $name, $orderManagerStub );


		$orderBaseItem = $orderBaseManagerStub->createItem();
		$orderBaseItem->setId( 1 );


		$orderBaseManagerStub->expects( $this->once() )->method( 'store' );

		$orderManagerStub->expects( $this->once() )->method( 'getSubManager' )
			->will( $this->returnValue( $orderBaseManagerStub ) );

		$orderManagerStub->expects( $this->once() )->method( 'saveItem' );


		$object = new \Aimeos\Controller\Frontend\Order\Standard( $context );
		$object->store( $orderBaseItem );
	}


	public function testBlock()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerFrontendOrderBlock';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Order\\Standard' )
			->setMethods( array( 'block' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\Controller\Common\Order\Factory::injectController( '\\Aimeos\\Controller\\Common\\Order\\' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'block' );


		$object = new \Aimeos\Controller\Frontend\Order\Standard( $context );
		$object->block( \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem() );
	}


	public function testUnblock()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerFrontendOrderUnblock';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Order\\Standard' )
			->setMethods( array( 'unblock' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\Controller\Common\Order\Factory::injectController( '\\Aimeos\\Controller\\Common\\Order\\' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'unblock' );


		$object = new \Aimeos\Controller\Frontend\Order\Standard( $context );
		$object->unblock( \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem() );
	}


	public function testUpdate()
	{
		$context = \TestHelper::getContext();
		$name = 'ControllerFrontendOrderUpdate';
		$context->getConfig()->set( 'classes/controller/common/order/name', $name );


		$orderCntlStub = $this->getMockBuilder( '\\Aimeos\\Controller\\Common\\Order\\Standard' )
			->setMethods( array( 'update' ) )
			->setConstructorArgs( array( $context ) )
			->getMock();

		\Aimeos\Controller\Common\Order\Factory::injectController( '\\Aimeos\\Controller\\Common\\Order\\' . $name, $orderCntlStub );

		$orderCntlStub->expects( $this->once() )->method( 'update' );


		$object = new \Aimeos\Controller\Frontend\Order\Standard( $context );
		$object->update( \Aimeos\MShop\Factory::createManager( $context, 'order' )->createItem() );
	}
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/license
 */

class MShop_Plugin_Provider_Order_ServicesUpdateTest
	extends PHPUnit_Framework_TestCase
{
	private $_order;
	private $_plugin;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Factory::createManager( $context, 'plugin' );
		$this->_plugin = $pluginManager->createItem();
		$this->_plugin->setProvider( 'ServicesUpdate' );
		$this->_plugin->setStatus( 1 );

		$orderBaseManager = MShop_Factory::createManager( $context, 'order/base' );
		$this->_order = $orderBaseManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->_plugin );
		unset( $this->_order );

		MShop_Factory::clear();
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate(TestHelper::getContext(), $this->_plugin );
		$object->register( $this->_order );
	}


	public function testUpdate()
	{
		$context = TestHelper::getContext();
		$object = new MShop_Plugin_Provider_Order_ServicesUpdate( $context, $this->_plugin );

		$orderBaseServiceManager = MShop_Factory::createManager( $context, 'order/base/service' );

		$serviceDelivery = $orderBaseServiceManager->createItem();
		$serviceDelivery->setId( 1 );
		$servicePayment = $orderBaseServiceManager->createItem();
		$servicePayment->setId( 2 );

		$this->_order->setService( $serviceDelivery, 'delivery' );
		$this->_order->setService( $servicePayment, 'payment' );


		$serviceStub = $this->getMockBuilder( 'MShop_Service_Manager_Default' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems', 'getProvider' ) )->getMock();

		MShop_Service_Manager_Factory::injectManager( 'MShop_Service_Manager_PluginServicesUpdate', $serviceStub );
		$context->getConfig()->set( 'classes/service/manager/name', 'PluginServicesUpdate' );

		$serviceItemDelivery = new MShop_Service_Item_Default( array( 'type' => 'delivery' ) );
		$serviceItemPayment = new MShop_Service_Item_Default( array( 'type' => 'payment' ) );

		$providerStub = $this->getMockBuilder( 'MShop_Service_Provider_Delivery_Manual' )
			->setConstructorArgs( array( $context, $serviceStub->createItem() ) )
			->setMethods( array( 'isAvailable' ) )->getMock();

		$serviceStub->expects( $this->once() )->method( 'searchItems' )
			->will( $this->returnValue( array( $serviceItemDelivery, $serviceItemPayment ) ) );

		$serviceStub->expects( $this->exactly( 2 ) )->method( 'getProvider' )
			->will( $this->returnValue( $providerStub ) );


		$this->assertTrue( $object->update( $this->_order, 'addProduct.after' ) );
		$this->assertNotSame( $serviceDelivery, $this->_order->getService( 'delivery' ) );
		$this->assertNotSame( $servicePayment, $this->_order->getService( 'payment' ) );
	}
}
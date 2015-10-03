<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 */

class MShop_Plugin_Provider_Order_ServicesAvailableTest extends PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $service;


	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @access protected
	 */
	protected function setUp()
	{
		$context = TestHelper::getContext();

		$pluginManager = MShop_Plugin_Manager_Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ServicesAvailable' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = MShop_Order_Manager_Factory::createManager( $context )->getSubManager( 'base' );
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );

		$this->order = $orderBaseManager->createItem();
		$this->service = $orderBaseServiceManager->createItem();
	}


	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @access protected
	 */
	protected function tearDown()
	{
		unset( $this->orderManager );
		unset( $this->plugin );
		unset( $this->service );
		unset( $this->order );
	}


	public function testRegister()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable( TestHelper::getContext(), $this->plugin );
		$object->register( $this->order );
	}

	public function testUpdateNone()
	{
		// MShop_Order_Item_Base_Base::PARTS_SERVICE not set, so update shall not be executed
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable( TestHelper::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after' ) );
	}

	public function testUpdateEmptyConfig()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable( TestHelper::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

		$this->order->setService( $this->service, 'payment' );
		$this->order->setService( $this->service, 'delivery' );
		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

	}

	public function testUpdateNoServices()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable( TestHelper::getContext(), $this->plugin );

		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE );
	}

	public function testUpdateWithServices()
	{
		$object = new MShop_Plugin_Provider_Order_ServicesAvailable( TestHelper::getContext(), $this->plugin );

		$this->order->setService( $this->service, 'payment' );
		$this->order->setService( $this->service, 'delivery' );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->setExpectedException( 'MShop_Plugin_Provider_Exception' );
		$object->update( $this->order, 'check.after', MShop_Order_Item_Base_Base::PARTS_SERVICE );
	}
}
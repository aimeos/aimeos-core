<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ServicesAvailableTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;
	private $plugin;
	private $service;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ServicesAvailable' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );

		$this->service = $orderBaseServiceManager->createItem();
		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesAvailable( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->plugin, $this->service, $this->order, $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'payment' => '1',
			'delivery' => '0',
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['payment'] );
		$this->assertEquals( null, $result['delivery'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'payment', $list );
		$this->assertArrayHasKey( 'delivery', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( '\Aimeos\MW\Criteria\Attribute\Iface', $entry );
		}
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateNone()
	{
		// \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE not set, so update shall not be executed
		$this->assertTrue( $this->object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateEmptyConfig()
	{
		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

	}


	public function testUpdateNoServices()
	{
		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );
	}


	public function testUpdateEmptyServices()
	{
		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->order->deleteService( 'payment' );
		$this->order->deleteService( 'delivery' );

		$this->plugin->setConfig( array(
			'delivery' => false,
			'payment' => false
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );
	}


	public function testUpdateWithServices()
	{
		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE ) );

		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_SERVICE );
	}
}
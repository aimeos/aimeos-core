<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ServicesAvailableTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;
	private $plugin;
	private $service;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->service = \Aimeos\MShop::create( $context, 'order/service' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesAvailable( $context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->plugin, $this->service, $this->order );
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
			$this->assertInstanceOf( \Aimeos\Base\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateNone()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateEmptyConfig()
	{
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );
	}


	public function testUpdateNoServices()
	{
		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
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
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
	}


	public function testUpdateWithServices()
	{
		$this->order->addService( $this->service, 'payment' );
		$this->order->addService( $this->service, 'delivery' );

		$this->plugin->setConfig( array(
				'delivery' => null,
				'payment' => null
		) );
		$part = ['order/service'];

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => true,
				'payment' => true
		) );

		$this->assertEquals( $part, $this->object->update( $this->order, 'check.after', $part ) );

		$this->plugin->setConfig( array(
				'delivery' => false,
				'payment' => false
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $part );
	}
}

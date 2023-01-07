<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class AddressesAvailableTest extends \PHPUnit\Framework\TestCase
{
	private $address;
	private $object;
	private $order;
	private $plugin;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners

		$this->address = \Aimeos\MShop::create( $context, 'order/address' )
			->create()->setLastName( 'Available' );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( $context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->plugin, $this->order, $this->address, $this->object );
	}


	public function testCheckConfigBE()
	{
		$result = $this->object->checkConfigBE( ['payment' => '1', 'delivery' => '0'] );

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
		$this->assertEquals( null, $this->object->setObject( $this->object )->update( $this->order, 'check.after' ) );
	}


	public function testEmptyConfig()
	{
		$value = ['order/address'];

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );

		$this->order->addAddress( $this->address, \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT );
		$this->order->addAddress( $this->address, \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY );

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );
	}


	public function testUpdateAddressesNotAvailable()
	{
		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => false,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => false
		) );
		$value = ['order/address'];

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => null,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => null
		) );

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => true,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => true
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}


	public function testUpdateAddressesAvailable()
	{
		$this->order->addAddress( $this->address, \Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT );
		$this->order->addAddress( $this->address, \Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => null,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => null
		) );
		$value = ['order/address'];

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => true,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => true
		) );

		$this->assertEquals( $value, $this->object->update( $this->order, 'check.after', $value ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_DELIVERY => false,
				\Aimeos\MShop\Order\Item\Address\Base::TYPE_PAYMENT => false
		) );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', $value );
	}
}

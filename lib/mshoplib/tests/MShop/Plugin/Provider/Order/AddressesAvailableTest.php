<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class AddressesAvailableTest extends \PHPUnit\Framework\TestCase
{
	private $address;
	private $object;
	private $order;
	private $plugin;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'AddressesAvailable' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$orderBaseAddressManager = $orderBaseManager->getSubManager( 'address' );

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->address = $orderBaseAddressManager->createItem();
		$this->address->setLastName( 'Available' );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ServicesAvailable( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->plugin, $this->order, $this->address, $this->object );
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
		// \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS not set, so update shall not be executed
		$this->assertTrue( $this->object->setObject( $this->object )->update( $this->order, 'check.after' ) );
	}


	public function testEmptyConfig()
	{
		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

		$this->assertTrue( $this->object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );
	}


	public function testUpdateAddressesNotAvailable()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( \TestHelperMShop::getContext(), $this->plugin );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => false,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => false
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => null,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => true,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => true
		) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS );
	}


	public function testUpdateAddressesAvailable()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( \TestHelperMShop::getContext(), $this->plugin );

		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => null,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => null
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => true,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => true
		) );

		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->plugin->setConfig( array(
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY => false,
				\Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT => false
		) );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS );
	}
}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class AddressesAvailableTest extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $address;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'AddressesAvailable' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context )->getSubManager( 'base' );
		$orderBaseAddressManager = $orderBaseManager->getSubManager( 'address' );

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->address = $orderBaseAddressManager->createItem();
		$this->address->setLastName( 'Available' );
	}


	protected function tearDown()
	{
		unset( $this->plugin );
		unset( $this->order );
		unset( $this->address );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}

	public function testUpdateNone()
	{
		// \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS not set, so update shall not be executed
		$object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( \TestHelperMShop::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after' ) );
	}

	public function testEmptyConfig()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\AddressesAvailable( \TestHelperMShop::getContext(), $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );

		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );
		$this->order->setAddress( $this->address, \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$this->assertTrue( $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_ADDRESS ) );
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
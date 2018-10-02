<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class AutofillTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $order;
	private $plugin;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'Autofill' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->plugin, $this->order, $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'autofill.address' => '1',
			'autofill.delivery' => '0',
			'autofill.deliverycode' => 'ship',
			'autofill.payment' => '1',
			'autofill.paymentcode' => 'pay',
			'autofill.useorder' => '0',
			'autofill.orderaddress' => '1',
			'autofill.orderservice' => '0',
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 8, count( $result ) );
		$this->assertEquals( null, $result['autofill.address'] );
		$this->assertEquals( null, $result['autofill.delivery'] );
		$this->assertEquals( null, $result['autofill.deliverycode'] );
		$this->assertEquals( null, $result['autofill.payment'] );
		$this->assertEquals( null, $result['autofill.paymentcode'] );
		$this->assertEquals( null, $result['autofill.useorder'] );
		$this->assertEquals( null, $result['autofill.orderaddress'] );
		$this->assertEquals( null, $result['autofill.orderservice'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 8, count( $list ) );
		$this->assertArrayHasKey( 'autofill.address', $list );
		$this->assertArrayHasKey( 'autofill.delivery', $list );
		$this->assertArrayHasKey( 'autofill.deliverycode', $list );
		$this->assertArrayHasKey( 'autofill.payment', $list );
		$this->assertArrayHasKey( 'autofill.paymentcode', $list );
		$this->assertArrayHasKey( 'autofill.useorder', $list );
		$this->assertArrayHasKey( 'autofill.orderaddress', $list );
		$this->assertArrayHasKey( 'autofill.orderservice', $list );

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
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );

	}


	public function testUpdateOrderNoItem()
	{
		$context = \TestHelperMShop::getContext();
		$context->setUserId( '' );

		$this->plugin->setConfig( array( 'autofill.useorder' => '1' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );
	}


	public function testUpdateOrderNone()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
		$context->setUserId( $manager->findItem( 'UTC001' )->getId() );

		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '0'
		) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );
	}


	public function testUpdateOrderAddress()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
		$context->setUserId( $manager->findItem( 'UTC001' )->getId() );


		$orderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseAddressStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Address\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems' ) )->getMock();

		$item1 = $orderBaseAddressStub->createItem();
		$item1->setType( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_DELIVERY );
		$item2 = $orderBaseAddressStub->createItem();
		$item2->setType( \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseAddressStub ) );
		$orderBaseAddressStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\PluginAutofill', $orderStub );
		$context->getConfig()->set( 'mshop/order/manager/name', 'PluginAutofill' );


		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '1',
			'autofill.orderservice' => '0'
		) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->order->getAddresses() ) );
		$this->assertEquals( [], $this->order->getServices() );
	}


	public function testUpdateOrderService()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
		$context->setUserId( $manager->findItem( 'UTC001' )->getId() );


		$orderStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'getSubManager' ) )->getMock();

		$orderBaseServiceStub = $this->getMockBuilder( '\\Aimeos\\MShop\\Order\\Manager\\Base\\Service\\Standard' )
			->setConstructorArgs( array( $context ) )->setMethods( array( 'searchItems' ) )->getMock();

		$item1 = $orderBaseServiceStub->createItem();
		$item1->setType( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY );
		$item1->setCode( 'unitcode' );

		$item2 = $orderBaseServiceStub->createItem();
		$item2->setType( \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT );
		$item2->setCode( 'unitpaymentcode' );

		$orderStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseStub ) );
		$orderBaseStub->expects( $this->any() )->method( 'getSubManager' )->will( $this->returnValue( $orderBaseServiceStub ) );
		$orderBaseServiceStub->expects( $this->once() )->method( 'searchItems' )->will( $this->returnValue( array( $item1, $item2 ) ) );

		\Aimeos\MShop\Order\Manager\Factory::injectManager( '\\Aimeos\\MShop\\Order\\Manager\\PluginAutofill', $orderStub );
		$context->getConfig()->set( 'mshop/order/manager/name', 'PluginAutofill' );


		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '1'
		) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( 2, count( $this->order->getServices() ) );
		$this->assertEquals( [], $this->order->getAddresses() );
	}


	public function testUpdateAddress()
	{
		$context = \TestHelperMShop::getContext();

		$customerManager = \Aimeos\MShop\Customer\Manager\Factory::createManager( $context );
		$context->setUserId( $customerManager->findItem( 'UTC001' )->getId() );

		$type = \Aimeos\MShop\Order\Item\Base\Address\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.address' => '1' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getServices() );
		$this->assertEquals( 1, count( $this->order->getAddresses() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Address\\Iface', $this->order->getAddress( $type ) );
	}


	public function testUpdateDelivery()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
		}
	}


	public function testUpdateDeliveryCode()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'unitcode' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
			$this->assertEquals( 'unitcode', $item->getCode() );
		}
	}


	public function testUpdateDeliveryCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'xyz' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
		}
	}


	public function testUpdatePayment()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
		}
	}


	public function testUpdatePaymentCode()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'unitpaymentcode' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item )
		{
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
			$this->assertEquals( 'unitpaymentcode', $item->getCode() );
		}
	}


	public function testUpdatePaymentCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'xyz' ) );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getService( $type ) ) );

		foreach( $this->order->getService( $type ) as $item ) {
			$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $item );
		}
	}
}
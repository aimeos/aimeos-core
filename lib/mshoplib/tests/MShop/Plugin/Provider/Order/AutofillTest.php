<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2016
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class AutofillTest extends \PHPUnit_Framework_TestCase
{
	private $plugin;
	private $orderManager;
	private $order;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'Autofill' );
		$this->plugin->setStatus( 1 );

		$this->orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners
	}


	protected function tearDown()
	{
		unset( $this->orderManager );
		unset( $this->plugin );
		unset( $this->order );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );

	}


	public function testUpdateOrderNoItem()
	{
		$context = \TestHelperMShop::getContext();
		$context->setUserId( '' );

		$this->plugin->setConfig( array( 'autofill.useorder' => '1' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );
	}


	public function testUpdateOrderNone()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer item for code UTC001" found' );
		}

		$context->setUserId( $customer->getId() );
		$this->plugin->setConfig( array(
			'autofill.useorder' => '1',
			'autofill.orderaddress' => '0',
			'autofill.orderservice' => '0'
		) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( $context, $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( [], $this->order->getServices() );
	}


	public function testUpdateOrderAddress()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'customer' );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer item for code UTC001" found' );
		}


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


		$context->setUserId( $customer->getId() );
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
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'customer.code', 'UTC001' ) );
		$result = $manager->searchItems( $search );

		if( ( $customer = reset( $result ) ) === false ) {
			throw new \RuntimeException( 'No customer item for code UTC001" found' );
		}


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


		$context->setUserId( $customer->getId() );
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
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
	}


	public function testUpdateDeliveryCode()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'unitcode' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
		$this->assertEquals( 'unitcode', $this->order->getService( $type )->getCode() );
	}


	public function testUpdateDeliveryCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
		$this->plugin->setConfig( array( 'autofill.delivery' => '1', 'autofill.deliverycode' => 'xyz' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
	}


	public function testUpdatePayment()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
	}


	public function testUpdatePaymentCode()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'unitpaymentcode' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
		$this->assertEquals( 'unitpaymentcode', $this->order->getService( $type )->getCode() );
	}


	public function testUpdatePaymentCodeNotExists()
	{
		$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_PAYMENT;
		$this->plugin->setConfig( array( 'autofill.payment' => '1', 'autofill.paymentcode' => 'xyz' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Autofill( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $object->update( $this->order, 'addProduct.after' ) );
		$this->assertEquals( [], $this->order->getAddresses() );
		$this->assertEquals( 1, count( $this->order->getServices() ) );
		$this->assertInstanceOf( '\\Aimeos\\MShop\\Order\\Item\\Base\\Service\\Iface', $this->order->getService( $type ) );
	}
}
<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ShippingTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $plugin;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $this->context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setStatus( '1' );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Shipping( $this->context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->plugin, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'threshold' => ['EUR' => '50.00'],
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 1, count( $result ) );
		$this->assertEquals( null, $result['threshold'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 1, count( $list ) );
		$this->assertArrayHasKey( 'threshold', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( '\Aimeos\MW\Criteria\Attribute\Iface', $entry );
		}
	}


	public function testRegister()
	{
		$order = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' )->createItem();

		$this->object->register( $order );
	}


	public function testUpdate()
	{
		$context = \TestHelperMShop::getContext();

		$this->plugin->setProvider( 'Shipping' );
		$this->plugin->setConfig( array( 'threshold' => array( 'EUR' => '34.00' ) ) );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$orderBaseProductManager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( $context );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC', 'IJKL' ) ) );
		$pResults = $manager->searchItems( $search, array( 'price' ) );

		if( count( $pResults ) !== 3 ) {
			throw new \RuntimeException( 'Wrong number of products' );
		}

		$products = [];
		foreach( $pResults as $prod ) {
			$products[$prod->getCode()] = $prod;
		}

		if( ( $price = current( $products['IJKL']->getRefItems( 'price' ) ) ) === false ) {
			throw new \RuntimeException( 'No price item found' );
		}
		$price->setValue( 10.00 );

		$product = $orderBaseProductManager->createItem();
		$product->copyFrom( $products['CNE'] );
		$product->setPrice( $price );

		$product2 = $orderBaseProductManager->createItem();
		$product2->copyFrom( $products['CNC'] );
		$product2->setPrice( $price );

		$product3 = $orderBaseProductManager->createItem();
		$product3->copyFrom( $products['IJKL'] );
		$product3->setPrice( $price );

		$orderBaseServiceManager = $orderBaseManager->getSubManager( 'service' );

		$serviceSearch = $orderBaseServiceManager->createSearch();
		$exp = array(
			$serviceSearch->compare( '==', 'order.base.service.type', 'delivery' ),
			$serviceSearch->compare( '==', 'order.base.service.costs', '5.00' )
		);
		$serviceSearch->setConditions( $serviceSearch->combine( '&&', $exp ) );
		$results = $orderBaseServiceManager->searchItems( $serviceSearch );

		if( ( $delivery = reset( $results ) ) === false ) {
			throw new \RuntimeException( 'No order base item found' );
		}

		$order = $orderBaseManager->createItem();
		$order->__sleep(); // remove event listeners

		$order->addService( $delivery, 'delivery' );
		$order->addProduct( $product );
		$order->addProduct( $product2 );
		$order->addProduct( $product3 );


		$this->assertEquals( 5.00, $order->getPrice()->getCosts() );
		$this->object->update( $order, 'addProduct' );

		$order->addProduct( $product );
		$this->object->update( $order, 'addProduct' );

		$this->assertEquals( 0.00, $order->getPrice()->getCosts() );
	}
}

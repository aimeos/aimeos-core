<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ShippingTest extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $object;
	private $plugin;
	private $product;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setProvider( 'Shipping' );
		$this->plugin->setConfig( array( 'threshold' => array( 'EUR' => '34.00' ) ) );
		$this->plugin->setStatus( '1' );

		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

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

		$this->product = $orderBaseProductManager->createItem();
		$this->product->copyFrom( $products['CNE'] );
		$this->product->setPrice( $price );

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

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->order->setService( $delivery, 'delivery' );
		$this->order->addProduct( $this->product );
		$this->order->addProduct( $product2 );
		$this->order->addProduct( $product3 );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Shipping( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->product );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\Shipping( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdate()
	{
		$this->assertEquals( 5.00, $this->order->getPrice()->getCosts() );
		$this->object->update( $this->order, 'addProduct' );

		$this->order->addProduct( $this->product );
		$this->object->update( $this->order, 'addProduct' );

		$this->assertEquals( 0.00, $this->order->getPrice()->getCosts() );
	}
}

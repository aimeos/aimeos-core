<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ShippingTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $plugin;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\Shipping( $this->context, $this->plugin );
	}


	protected function tearDown() : void
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
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$order = \Aimeos\MShop::create( $this->context, 'order/base' )->create();
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $order ) );
	}


	public function testUpdate()
	{
		$this->plugin = $this->plugin->setProvider( 'Shipping' )
			->setConfig( ['threshold' => ['EUR' => '34.00']] );

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', ['CNE', 'CNC', 'IJKL'] ) );

		$products = [];
		foreach( $manager->search( $search, ['price'] ) as $prod ) {
			$products[$prod->getCode()] = $prod;
		}

		if( count( $products ) !== 3 ) {
			throw new \RuntimeException( 'Wrong number of products' );
		}

		if( ( $price = $products['IJKL']->getRefItems( 'price' )->first() ) === null ) {
			throw new \RuntimeException( 'No price item found' );
		}
		$price = $price->setValue( 10.00 );

		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );
		$product = $orderBaseProductManager->create()->copyFrom( $products['CNE'] )->setPrice( $price );
		$product2 = $orderBaseProductManager->create()->copyFrom( $products['CNC'] )->setPrice( $price );
		$product3 = $orderBaseProductManager->create()->copyFrom( $products['IJKL'] )->setPrice( $price );

		$orderBaseServiceManager = \Aimeos\MShop::create( $this->context, 'order/base/service' );
		$serviceSearch = $orderBaseServiceManager->filter();
		$exp = array(
			$serviceSearch->compare( '==', 'order.base.service.type', 'delivery' ),
			$serviceSearch->compare( '==', 'order.base.service.costs', '5.00' )
		);
		$serviceSearch->setConditions( $serviceSearch->and( $exp ) );
		$delivery = $orderBaseServiceManager->search( $serviceSearch )->first();

		$order = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off(); // remove event listeners

		$order = $order->addService( $delivery, 'delivery' )
			->addProduct( $product )->addProduct( $product2 )->addProduct( $product3 );


		$this->assertEquals( 5.00, $order->getPrice()->getCosts() );
		$this->assertEquals( null, $this->object->update( $order, 'addProduct' ) );

		$order->addProduct( $product );
		$this->assertEquals( null, $this->object->update( $order, 'addProduct' ) );

		$this->assertEquals( 0.00, $order->getPrice()->getCosts() );
	}
}

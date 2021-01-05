<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductLimitTest extends \PHPUnit\Framework\TestCase
{
	private $context;
	private $object;
	private $order;
	private $plugin;
	private $products;


	protected function setUp() : void
	{
		$this->context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create()->setConfig( ['single-number-max' => 10] );
		$this->order = \Aimeos\MShop::create( $this->context, 'order/base' )->create()->off(); // remove event listeners

		$this->products = [];
		$orderBaseProductManager = \Aimeos\MShop::create( $this->context, 'order/base/product' );

		$manager = \Aimeos\MShop\Product\Manager\Factory::create( \TestHelperMShop::getContext() );
		$search = $manager->filter();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );

		foreach( $manager->search( $search )->toArray() as $product ) {
			$this->products[$product->getCode()] = $orderBaseProductManager->create()->copyFrom( $product );
		}

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductLimit( $this->context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->order, $this->plugin, $this->products, $this->context );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'single-number-max' => '10',
			'total-number-max' => '100',
			'single-value-max' => ['EUR' => '100.00'],
			'total-value-max' => ['EUR' => '1000.00'],
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 4, count( $result ) );
		$this->assertEquals( null, $result['single-number-max'] );
		$this->assertEquals( null, $result['total-number-max'] );
		$this->assertEquals( null, $result['single-value-max'] );
		$this->assertEquals( null, $result['total-value-max'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 4, count( $list ) );
		$this->assertArrayHasKey( 'single-number-max', $list );
		$this->assertArrayHasKey( 'total-number-max', $list );
		$this->assertArrayHasKey( 'single-value-max', $list );
		$this->assertArrayHasKey( 'total-value-max', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateSingleNumberMax()
	{
		$this->plugin->setConfig( ['single-number-max' => 10] );
		$product = $this->products['CNC']->setQuantity( 10 );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );


		$product = $this->products['CNE']->setQuantity( 11 );

		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $product );
	}


	public function testUpdateSingleValueMax()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$this->plugin->setConfig( ['single-value-max' => ['EUR' => '10.00']] );

		$product = $this->products['CNC']->setQuantity( 1 )
			->setPrice( $priceManager->create()->setValue( '10.00' ) );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );


		$product = $this->products['CNE']->setQuantity( 3 )
			->setPrice( $priceManager->create()->setValue( '3.50' ) );

		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $product );
	}


	public function testUpdateTotalNumberMax()
	{
		$this->plugin->setConfig( array( 'total-number-max' => 10 ) );
		$product = $this->products['CNC']->setQuantity( 10 );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );


		$this->order->addProduct( $this->products['CNC'] );
		$product = $this->products['CNE']->setQuantity( 1 );

		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $product );
	}


	public function testUpdateTotalValueMax()
	{
		$priceManager = \Aimeos\MShop::create( $this->context, 'price' );
		$this->plugin->setConfig( ['total-value-max' => ['EUR' => '110.00']] );

		$product = $this->products['CNC']->setQuantity( 1 )
			->setPrice( $priceManager->create()->setValue( '100.00' ) );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );


		$this->order->addProduct( $this->products['CNC'] );
		$product = $this->products['CNE']->setQuantity( 2 )
			->setPrice( $priceManager->create()->setValue( '10.00' ) );

		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $product );
	}
}

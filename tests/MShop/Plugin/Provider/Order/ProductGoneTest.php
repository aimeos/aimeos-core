<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2024
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductGoneTest extends \PHPUnit\Framework\TestCase
{
	private $order;
	private $object;
	private $product;
	private $context;


	protected function setUp() : void
	{
		$this->context = \TestHelper::context();
		$plugin = \Aimeos\MShop::create( $this->context, 'plugin' )->create();

		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$newProduct = $manager->find( 'CNE' )->setId( null )->setLabel( 'Bad Product' )->setCode( 'WTF' );
		$this->product = $manager->save( $newProduct );

		$manager = \Aimeos\MShop::create( $this->context, 'order' );
		$search = $manager->filter()->add( 'order.price', '==', 672.00 )->slice( 0, 1 );
		$this->order = $manager->search( $search )->first( new \RuntimeException( 'No order item found' ) )->off();

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( $this->context, $plugin );
	}


	protected function tearDown() : void
	{
		$manager = \Aimeos\MShop::create( $this->context, 'product' );
		$manager->delete( $manager->find( 'WTF' )->getId() );

		unset( $this->object, $this->product, $this->order, $this->context );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateNone()
	{
		$this->assertEquals( null, $this->object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$type = ['order/product'];
		$this->assertEquals( $type, $this->object->update( $this->order, 'check.after', $type ) );
	}


	public function testUpdateProductDeleted()
	{
		$badItem = \Aimeos\MShop::create( $this->context, 'order/product' )->create()
			->setProductId( -13 )->setProductCode( 'NONE' );

		$this->order->addProduct( $badItem );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', ['order/product'] );
	}


	public function testUpdateProductEnded()
	{
		$badItem = \Aimeos\MShop::create( $this->context, 'order/product' )
			->create()->copyFrom( $this->product );

		$this->product->setDateEnd( '1999-12-31 23:59:59' );
		\Aimeos\MShop::create( $this->context, 'product' )->save( $this->product );

		$this->order->addProduct( $badItem );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', ['order/product'] );
	}


	public function testUpdateProductNotStarted()
	{
		$badItem = \Aimeos\MShop::create( $this->context, 'order/product' )
			->create()->copyFrom( $this->product );

		$this->product->setDateStart( '2100-12-31 23:59:59' );
		\Aimeos\MShop::create( $this->context, 'product' )->save( $this->product );

		$this->order->addProduct( $badItem );

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', ['order/product'] );
	}


	public function testUpdateProductDeactivated()
	{
		$badItem = \Aimeos\MShop::create( $this->context, 'order/product' )
			->create()->copyFrom( $this->product );

		\Aimeos\MShop::create( $this->context, 'product' )->save( $this->product->setStatus( 0 ) );

		$this->order->addProduct( $badItem );
		$badItemPosition = $this->order->getProducts()->firstKey();

		$this->expectException( \Aimeos\MShop\Plugin\Provider\Exception::class );
		$this->object->update( $this->order, 'check.after', ['order/product'] );
	}
}

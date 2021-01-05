<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class FreeProductTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $orderProduct;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$address = \Aimeos\MShop::create( $context, 'order/base/address' )->create()->setEmail( 'test@example.com' );

		$manager = \Aimeos\MShop::create( $context, 'order/base/product' );
		$this->orderProduct = $manager->create()->setProductCode( 'ABCD' );
		$this->orderProduct = $this->orderProduct->setPrice( $this->orderProduct->getPrice()->setValue( '100.00' ) );

		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->create()->off(); // remove event listeners
		$this->order->addAddress( $address, 'payment' )->addProduct( $this->orderProduct );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\FreeProduct( $context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->plugin, $this->orderProduct, $this->order, $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'productcode' => 'ABCD',
			'count' => 1,
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['productcode'] );
		$this->assertEquals( null, $result['count'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'productcode', $list );
		$this->assertArrayHasKey( 'count', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( \Aimeos\MW\Criteria\Attribute\Iface::class, $entry );
		}
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdateInvalid()
	{
		$this->expectException( \Aimeos\MW\Common\Exception::class );
		$this->object->update( $this->order, 'addProduct.after' );
	}


	public function testUpdateWrongProductCode()
	{
		$this->orderProduct->setProductCode( 'xyz' );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateNoAddress()
	{
		$this->order->deleteAddress( 'payment' );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateCountExceeded()
	{
		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdate()
	{
		$this->plugin->setConfig( ['productcode' => 'ABCD', 'count' => 5] );

		$this->assertEquals( $this->orderProduct, $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getRebate() );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class FreeProductTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $orderAddress;
	private $orderProduct;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'plugin' );
		$this->plugin = $manager->createItem();
		$this->plugin->setProvider( 'FreeProduct' );
		$this->plugin->setConfig( ['freeproduct.productcode' => 'ABCD', 'freeproduct.count' => 2] );
		$this->plugin->setStatus( 1 );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/address' );
		$this->orderAddress = $manager->createItem();
		$this->orderAddress->setEmail( 'test@example.com' );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'order/base/product' );
		$this->orderProduct = $manager->createItem();
		$this->orderProduct->setProductCode( 'ABCD' );
		$this->orderProduct->getPrice()->setValue( '100.00' );

		$manager = \Aimeos\MShop\Factory::createManager( $context, 'order/base' );
		$this->order = $manager->createItem();
		$this->order->__sleep(); // remove event listeners
		$this->order->setAddress( $this->orderAddress, 'payment' );
		$this->order->addProduct( $this->orderProduct );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\FreeProduct( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->plugin, $this->orderProduct, $this->orderAddress, $this->order, $this->object );
	}


	public function testCheckConfigBE()
	{
		$attributes = array(
			'freeproduct.productcode' => 'ABCD',
			'freeproduct.count' => 1,
		);

		$result = $this->object->checkConfigBE( $attributes );

		$this->assertEquals( 2, count( $result ) );
		$this->assertEquals( null, $result['freeproduct.productcode'] );
		$this->assertEquals( null, $result['freeproduct.count'] );
	}


	public function testGetConfigBE()
	{
		$list = $this->object->getConfigBE();

		$this->assertEquals( 2, count( $list ) );
		$this->assertArrayHasKey( 'freeproduct.productcode', $list );
		$this->assertArrayHasKey( 'freeproduct.count', $list );

		foreach( $list as $entry ) {
			$this->assertInstanceOf( '\Aimeos\MW\Criteria\Attribute\Iface', $entry );
		}
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateInvalid()
	{
		$this->setExpectedException( '\\Aimeos\\MW\\Common\\Exception' );
		$this->object->update( $this->order, 'addProduct.after' );
	}


	public function testUpdateWrongProductCode()
	{
		$this->orderProduct->setProductCode( 'xyz' );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateNoAddress()
	{
		$this->order->deleteAddress( 'payment' );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdateCountExceeded()
	{
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getValue() );
	}


	public function testUpdate()
	{
		$this->plugin->setConfig( ['freeproduct.productcode' => 'ABCD', 'freeproduct.count' => 5] );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.after', $this->orderProduct ) );
		$this->assertEquals( '0.00', $this->orderProduct->getPrice()->getValue() );
		$this->assertEquals( '100.00', $this->orderProduct->getPrice()->getRebate() );
	}
}
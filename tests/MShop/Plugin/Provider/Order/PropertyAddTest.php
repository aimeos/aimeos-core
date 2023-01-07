<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyAddTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $product;


	protected function setUp() : void
	{
		$context = \TestHelper::context();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order' )->create()->off(); // remove event listeners

		$product = \Aimeos\MShop::create( $context, 'product' )->find( 'CNC' );
		$this->product = \Aimeos\MShop::create( $context, 'order/product' )->create()->copyFrom( $product );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyAdd( $context, $this->plugin );
	}


	protected function tearDown() : void
	{
		unset( $this->object, $this->order, $this->plugin, $this->product );
	}


	public function testRegister()
	{
		$this->assertInstanceOf( \Aimeos\MShop\Plugin\Provider\Iface::class, $this->object->register( $this->order ) );
	}


	public function testUpdate()
	{
		$product = $this->product;
		$this->plugin->setConfig( ['types' => ['package-width']] );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );
		$this->assertEquals( [$product], $this->object->update( $this->order, 'addProduct.before', [$product] ) );

		$attributes = $this->product->getAttributeItems();
		$this->assertEquals( 1, count( $attributes ) );
		$this->assertEquals( 'product/property', $attributes->first()->getType() );
		$this->assertEquals( 'package-width', $attributes->first()->getCode() );
		$this->assertEquals( '15.0', $attributes->first()->getValue() );
	}


	public function testUpdateNone()
	{
		$this->plugin->setConfig( ['types' => ['unknown']] );

		$this->assertEquals( $this->product, $this->object->update( $this->order, 'addProduct.before', $this->product ) );
		$this->assertEquals( 0, count( $this->product->getAttributeItems() ) );
	}
}

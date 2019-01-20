<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyAddTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $product;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->createItem();

		$product = \Aimeos\MShop::create( $context, 'product' )->findItem( 'CNC' );
		$this->product = \Aimeos\MShop::create( $context, 'order/base/product' )->createItem()->copyFrom( $product );

		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyAdd( $context, $this->plugin );
	}


	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->product );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdate()
	{
		$product = $this->product;
		$this->plugin->setConfig( ['types' => ['package-width']] );

		$this->assertEquals( $product, $this->object->update( $this->order, 'addProduct.before', $product ) );
		$this->assertEquals( [$product], $this->object->update( $this->order, 'addProduct.before', [$product] ) );

		$attributes = $this->product->getAttributeItems();
		$this->assertEquals( 1, count( $attributes ) );
		$this->assertEquals( 'product/property', reset( $attributes )->getType() );
		$this->assertEquals( 'package-width', reset( $attributes )->getCode() );
		$this->assertEquals( '15.0', reset( $attributes )->getValue() );
	}


	public function testUpdateNone()
	{
		$this->plugin->setConfig( ['types' => ['unknown']] );

		$this->assertEquals( $this->product, $this->object->update( $this->order, 'addProduct.before', $this->product ) );
		$this->assertEquals( 0, count( $this->product->getAttributeItems() ) );
	}
}

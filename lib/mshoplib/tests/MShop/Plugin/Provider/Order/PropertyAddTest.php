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
		$this->plugin = \Aimeos\MShop\Plugin\Manager\Factory::create( $context )->createItem();

		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::create( $context )->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$product = \Aimeos\MShop\Product\Manager\Factory::create( $context )->findItem( 'CNC' );
		$this->product = $orderBaseProductManager->createItem()->copyFrom( $product );

		$this->order = $orderBaseManager->createItem();
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
		$this->plugin->setConfig( ['types' => ['package-width']] );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->product ) );
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', [$this->product] ) );

		$attributes = $this->product->getAttributeItems();
		$this->assertEquals( 1, count( $attributes ) );
		$this->assertEquals( 'product/property', reset( $attributes )->getType() );
		$this->assertEquals( 'package-width', reset( $attributes )->getCode() );
		$this->assertEquals( '15.0', reset( $attributes )->getValue() );
	}


	public function testUpdateNone()
	{
		$this->plugin->setConfig( ['types' => ['unknown']] );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->product ) );
		$this->assertEquals( 0, count( $this->product->getAttributeItems() ) );
	}
}

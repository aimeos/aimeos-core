<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyMatchTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $product;


	protected function setUp() : void
	{
		$context = \TestHelperMShop::getContext();
		$this->plugin = \Aimeos\MShop::create( $context, 'plugin' )->create();
		$this->order = \Aimeos\MShop::create( $context, 'order/base' )->create()->off(); // remove event listeners

		$product = \Aimeos\MShop::create( $context, 'product' )->find( 'CNC' );
		$this->product = \Aimeos\MShop::create( $context, 'order/base/product' )->create()->copyFrom( $product );

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( $context, $this->plugin );
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
		$this->plugin->setConfig( ['values' => ['package-height' => '10.0']] );
		$this->assertEquals( $this->product, $this->object->update( $this->order, 'addProduct.before', $this->product ) );
	}


	public function testUpdateFail()
	{
		$this->plugin->setConfig( ['values' => ['package-height' => 0]] );

		$this->expectException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $this->product );
	}
}

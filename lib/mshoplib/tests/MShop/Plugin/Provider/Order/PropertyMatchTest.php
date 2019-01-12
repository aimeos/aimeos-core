<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2018
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyMatchTest extends \PHPUnit\Framework\TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $product;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::create( $context );
		$this->plugin = $pluginManager->createItem();

		$orderBaseManager = \Aimeos\MShop\Order\Manager\Factory::create( $context )->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$product = \Aimeos\MShop\Product\Manager\Factory::create( $context )->findItem( 'CNC' );
		$this->product = $orderBaseProductManager->createItem()->copyFrom( $product );

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( $context, $this->plugin );
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
		$this->plugin->setConfig( ['propertymatch.values' => ['package-height' => '10.0']] );
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->product ) );
	}


	public function testUpdateTwoConditions()
	{
		$this->plugin->setConfig( ['propertymatch.values' => ['package-height' => '10.0', 'package-length' => '20.0']] );
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->product ) );
	}


	public function testUpdateFail()
	{
		$this->plugin->setConfig( ['propertymatch.values' => ['package-height' => 0]] );

		$this->setExpectedException( \Aimeos\MShop\Plugin\Exception::class );
		$this->object->update( $this->order, 'addProduct.before', $this->product );
	}
}

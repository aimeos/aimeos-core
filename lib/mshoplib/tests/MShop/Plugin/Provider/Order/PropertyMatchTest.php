<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class PropertyMatchTest extends \PHPUnit_Framework_TestCase
{
	private $object;
	private $plugin;
	private $order;
	private $products;


	protected function setUp()
	{
		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setTypeId( 2 );
		$this->plugin->setProvider( 'PropertyMatch' );
		$this->plugin->setConfig( array( 'product.label' => 'Cafe Noire Cappuccino' ) );
		$this->plugin->setStatus( '1' );


		$orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$orderBaseManager = $orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$manager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', array( 'CNE', 'CNC' ) ) );

		$products = $manager->searchItems( $search );

		if( count( $products ) !== 2 ) {
			throw new \RuntimeException( 'Wrong number of products' );
		}

		$this->products = [];

		foreach( $products as $product )
		{
			$item = $orderBaseProductManager->createItem();
			$item->copyFrom( $product );

			$this->products[$product->getCode()] = $item;
		}

		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove event listeners

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

	}


	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->products );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateOk()
	{
		// single condition
		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );

		$this->plugin->setConfig( array( 'product.property.type.code' => 'package-height' ) );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		// two conditions
		$this->plugin->setConfig( array(
			'product.property.type.code' => 'package-height',
			'product.label' => 'Cafe Noire Cappuccino',
		) );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );
	}


	public function testUpdateFail()
	{
		$this->plugin->setConfig( array( 'product.label' => 'wrong label' ) );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}


	public function testUpdateFailMultipleConditions()
	{
		$this->plugin->setConfig( array(
			'product.property.type.code' => 'package-height',
			'product.label' => 'wrong label',
		) );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}

	public function testUpdateFailList()
	{
		$this->plugin->setConfig( array( 'product.lists.domain' => 'foobar' ) );
		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\PropertyMatch( \TestHelperMShop::getContext(), $this->plugin );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] );
	}
}

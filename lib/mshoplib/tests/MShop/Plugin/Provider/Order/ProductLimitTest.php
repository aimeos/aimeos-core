<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductLimitTest extends \PHPUnit_Framework_TestCase
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
		$this->plugin->setProvider( 'ProductLimit' );
		$this->plugin->setConfig( array( 'single-number-max' => 10 ) );
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

		$this->object = new \Aimeos\MShop\Plugin\Provider\Order\ProductLimit( \TestHelperMShop::getContext(), $this->plugin );

	}


	protected function tearDown()
	{
		unset( $this->object, $this->order, $this->plugin, $this->products );
	}


	public function testRegister()
	{
		$this->object->register( $this->order );
	}


	public function testUpdateSingleNumberMax()
	{
		$this->plugin->setConfig( array( 'single-number-max' => 10 ) );


		$this->products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->products['CNE']->setQuantity( 11 );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateSingleValueMax()
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$this->plugin->setConfig( array( 'single-value-max' => array( 'EUR' => '10.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->products['CNC']->setPrice( $price );
		$this->products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$price = $priceManager->createItem();
		$price->setValue( '3.50' );

		$this->products['CNE']->setPrice( $price );
		$this->products['CNE']->setQuantity( 3 );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateTotalNumberMax()
	{
		$this->plugin->setConfig( array( 'total-number-max' => 10 ) );


		$this->products['CNC']->setQuantity( 10 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->order->addProduct( $this->products['CNC'] );
		$this->products['CNE']->setQuantity( 1 );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}


	public function testUpdateTotalValueMax()
	{
		$priceManager = \Aimeos\MShop\Price\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$this->plugin->setConfig( array( 'total-value-max' => array( 'EUR' => '110.00' ) ) );


		$price = $priceManager->createItem();
		$price->setValue( '100.00' );

		$this->products['CNC']->setPrice( $price );
		$this->products['CNC']->setQuantity( 1 );

		$this->assertTrue( $this->object->update( $this->order, 'addProduct.before', $this->products['CNC'] ) );


		$this->order->addProduct( $this->products['CNC'] );

		$price = $priceManager->createItem();
		$price->setValue( '10.00' );

		$this->products['CNE']->setPrice( $price );
		$this->products['CNE']->setQuantity( 2 );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Exception' );
		$this->object->update( $this->order, 'addProduct.before', $this->products['CNE'] );
	}
}

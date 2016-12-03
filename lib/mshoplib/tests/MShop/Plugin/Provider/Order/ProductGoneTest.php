<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductGoneTest extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $product;
	private $orderManager;


	protected function setUp()
	{
		$context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Plugin\Manager\Factory::createManager( $context );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductGone' );
		$this->plugin->setStatus( 1 );

		$this->orderManager = \Aimeos\MShop\Order\Manager\Factory::createManager( $context );
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );

		$search = $orderBaseManager->createSearch();
		$search->setConditions( $search->compare( '==', 'order.base.price', 672.00 ) );
		$search->setSlice( 0, 1 );
		$items = $orderBaseManager->searchItems( $search );
		if( ( $baseItem = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'No order base item found.' );
		}

		$this->order = $baseItem;

		// create a product to mess with in the tests
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'CNE' ) );
		$search->setSlice( 0, 1 );
		$items = $productManager->searchItems( $search );
		if( ( $newProduct = reset( $items ) ) === false ) {
			throw new \RuntimeException( 'Product code "CNE" not found.' );
		}

		$newProduct->setId( null );
		$newProduct->setLabel( 'Bad Product' );
		$newProduct->setCode( 'WTF' );
		$productManager->saveItem( $newProduct );

		$this->product = $newProduct;
	}


	protected function tearDown()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', 'WTF' ) );
		$items = $productManager->searchItems( $search );

		foreach( $items as $badItem ) {
			$productManager->deleteItem( $badItem->getId() );
		}

		unset( $this->orderManager );
		unset( $this->plugin );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		// \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT not set, so check shall not be executed
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );
		$this->AssertTrue( $object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );
		$result = $object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );

		$this->assertTrue( $result );
	}


	public function testUpdateProductDeleted()
	{
		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );

		$badItem = $orderBaseProductManager->createItem();
		$badItem->setProductId( -13 );
		$badItem->setProductCode( 'NONE' );

		$this->order->addProduct( $badItem );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );

		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateProductEnded()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setDateEnd( '1999-12-31 23:59:59' );

		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );
		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateProductNotStarted()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setDateStart( '2022-12-31 23:59:59' );

		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );
		$this->setExpectedException( '\\Aimeos\\MShop\\Plugin\\Provider\\Exception' );
		$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
	}


	public function testUpdateProductDeactivated()
	{
		$productManager = \Aimeos\MShop\Product\Manager\Factory::createManager( \TestHelperMShop::getContext() );

		$orderBaseManager = $this->orderManager->getSubManager( 'base' );
		$orderBaseProductManager = $orderBaseManager->getSubManager( 'product' );
		$badItem = $orderBaseProductManager->createItem();
		$badItem->copyFrom( $this->product );

		$this->product->setStatus( 0 );
		$productManager->saveItem( $this->product );

		$this->order->addProduct( $badItem );
		$products = $this->order->getProducts();

		if( count( $products ) < 1 ) {
			throw new \RuntimeException( 'Product for testing not in basket.' );
		}

		$badItemPosition = key( $products );

		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductGone( \TestHelperMShop::getContext(), $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
			$this->fail( '\Aimeos\MShop\Plugin\Provider\Exception not thrown.' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$ref = array( 'product' => array( $badItemPosition => 'gone.status' ) );
			$this->assertEquals( $ref, $e->getErrorCodes() );
		}
	}
}

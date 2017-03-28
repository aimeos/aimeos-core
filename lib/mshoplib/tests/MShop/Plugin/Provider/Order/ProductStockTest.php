<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


class ProductStockTest extends \PHPUnit_Framework_TestCase
{
	private $order;
	private $plugin;
	private $context;


	protected function setUp()
	{
		$this->context = \TestHelperMShop::getContext();

		$pluginManager = \Aimeos\MShop\Factory::createManager( $this->context, 'plugin' );
		$this->plugin = $pluginManager->createItem();
		$this->plugin->setProvider( 'ProductCode' );
		$this->plugin->setStatus( 1 );

		$orderBaseManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base' );
		$this->order = $orderBaseManager->createItem();
		$this->order->__sleep(); // remove plugins
	}


	protected function tearDown()
	{
		unset( $this->plugin, $this->order, $this->context );
	}


	public function testRegister()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );
		$object->register( $this->order );
	}


	public function testUpdateNone()
	{
		// \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT not set, so update shall not be executed
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after' ) );
	}


	public function testUpdateOk()
	{
		$constant = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );
		$this->assertTrue( $object->update( $this->order, 'check.after', $constant ) );
	}


	public function testUpdateOutOfStock()
	{
		$this->order->addProduct( $this->getOrderProduct( 'EFGH' ) );
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
			throw new \RuntimeException( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( array( 'product' => array( '0' => 'stock.notenough' ) ), $e->getErrorCodes() );
			$this->assertEquals( [], $this->order->getProducts() );
		}
	}


	public function testUpdateNoStockItem()
	{
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );
		$this->order->addProduct( $this->getOrderProduct( 'QRST' ) );

		try
		{
			$object->update( $this->order, 'check.after', \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT );
			throw new \RuntimeException( 'Expected exception not thrown' );
		}
		catch( \Aimeos\MShop\Plugin\Provider\Exception $e )
		{
			$this->assertEquals( array( 'product' => array( '0' => 'stock.notenough' ) ), $e->getErrorCodes() );
			$this->assertEquals( [], $this->order->getProducts() );
		}
	}


	public function testUpdateStockUnlimited()
	{
		$const = \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT;
		$object = new \Aimeos\MShop\Plugin\Provider\Order\ProductStock( $this->context, $this->plugin );

		$orderProduct = $this->getOrderProduct( 'MNOP' );
		$orderProduct->setStockType( 'unit_type4' );

		$this->order->addProduct( $orderProduct );

		$this->assertTrue( $object->update( $this->order, 'check.after', $const ) );
	}


	/**
	 * Returns an order product item
	 *
	 * @param string $code Unique product code
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface Order product item
	 */
	protected function getOrderProduct( $code )
	{
		$productManager = \Aimeos\MShop\Factory::createManager( $this->context, 'product' );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.code', $code ) );
		$productItems = $productManager->searchItems( $search );

		if( ( $productItem = reset( $productItems ) ) == false ) {
			throw new \RuntimeException( 'No product item found' );
		}

		$orderProductManager = \Aimeos\MShop\Factory::createManager( $this->context, 'order/base/product' );
		$orderProductItem = $orderProductManager->createItem();
		$orderProductItem->copyFrom( $productItem );

		return $orderProductItem;
	}
}
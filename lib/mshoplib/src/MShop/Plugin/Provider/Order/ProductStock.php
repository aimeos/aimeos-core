<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the products in a basket for sufficient stocklevel
 *
 * Notifies the customers if one or more products have gone out of stock in the
 * meantime. They have to remove this products before they can continue in the
 * checkout process.
 *
 * Also, the plugin reduces the product quantity automatically if there are not
 * enough products in stock.
 *
 * The checks are executed for the basket and checkout summary view.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductStock
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 * @return \Aimeos\MShop\Plugin\Provider\Iface Plugin object for method chaining
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p ) : \Aimeos\MW\Observer\Listener\Iface
	{
		$p->attach( $this->getObject(), 'addProduct.after' );
		$p->attach( $this->getObject(), 'check.after' );
		return $this;
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @return mixed Modified value parameter
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, string $action, $value = null )
	{
		if( ( is_int( $value ) && ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) ) === 0 ) {
			return $value;
		}

		\Aimeos\MW\Common\Base::checkClass( \Aimeos\MShop\Order\Item\Base\Iface::class, $order );

		if( !$order->getProducts()->isEmpty() && ( $outOfStock = $this->checkStock( $order ) ) !== [] )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Products out of stock' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, array( 'product' => $outOfStock ) );
		}

		return $value;
	}


	/**
	 * Checks if all products in the basket have enough stock
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket object
	 * @return array Associative list of basket product positions as keys and the error codes as values
	 * @todo 2022.01 Store product ID and article ID separately in order product
	 */
	protected function checkStock( \Aimeos\MShop\Order\Item\Base\Iface $order ) : array
	{
		$context = $this->getContext();
		$expr = $stockExpr = $stockMap = [];

		$manager = \Aimeos\MShop::create( $context, 'product' );
		$filter = $manager->filter();

		foreach( $order->getProducts() as $orderProduct )
		{
			$expr[] = $filter->and( [
				$filter->is( 'product.code', '==', $orderProduct->getProductCode() ),
				$filter->is( 'product.siteid', '==', $orderProduct->getSitePath() )
			] );
		}

		$filter->add( $filter->or( $expr ) )->slice( 0, count( $expr ) );

		$products = $manager->search( $filter )->col( 'product.id', 'product.code' );

		foreach( $order->getProducts() as $orderProduct )
		{
			if( isset( $products[$orderProduct->getProductCode()] ) )
			{
				$stockExpr[] = $filter->and( [
					$filter->is( 'stock.productid', '==', $products[$orderProduct->getProductCode()] ),
					$filter->is( 'stock.siteid', '==', $orderProduct->getSiteId() ),
					$filter->is( 'stock.type', '==', $orderProduct->getStockType() )
				] );
			}
		}

		$stockManager = \Aimeos\MShop::create( $context, 'stock' );
		$filter = $stockManager->filter();
		$filter->add( $filter->or( $stockExpr ) )->slice( 0, count( $stockExpr ) );
		$products = $products->flip();

		foreach( $stockManager->search( $filter ) as $stockItem )
		{
			$code = $products[$stockItem->getProductId()] ?? null;
			$stockMap[$stockItem->getSiteId()][$code][$stockItem->getType()] = $stockItem;
		}

		return $this->checkStockLevels( $order, $stockMap );
	}


	/**
	 * Checks if the products in the basket have enough stock
	 *
	 * Removes products from the basket which are out of stock and decreases the
	 * quantities of orders products if there's not enough stock.
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket object
	 * @param array $stockMap Multi-dimensional associative list of product ID / stock type as keys and stock level as values
	 * @return array Associative list of basket positions as keys and error codes as values
	 */
	protected function checkStockLevels( \Aimeos\MShop\Order\Item\Base\Iface $order, array $stockMap ) : array
	{
		$outOfStock = [];
		$products = $order->getProducts();

		foreach( $products as $pos => $orderProduct )
		{
			$stocklevel = 0;
			$siteid = $orderProduct->getSiteId();
			$type = $orderProduct->getStockType();
			$code = $orderProduct->getProductCode();

			if( isset( $stockMap[$siteid][$code][$type] ) )
			{
				$stockItem = $stockMap[$siteid][$code][$type];
				$orderProduct->setTimeFrame( $stockItem->getTimeFrame() );

				if( ( $stocklevel = $stockItem->getStockLevel() ) === null ) {
					continue;
				}

				if( $stocklevel >= $orderProduct->getQuantity() )
				{
					$stock = $stockItem->getStockLevel() - $orderProduct->getQuantity();
					$stockItem->setStockLevel( $stock );
					continue;
				}
			}

			if( $stocklevel > 0 ) { // update quantity to actual stock level
				$order->addProduct( $orderProduct->setQuantity( $stocklevel ), $pos );
			} else {
				$order->deleteProduct( $pos );
			}

			$outOfStock[$pos] = 'stock.notenough';
		}

		return $outOfStock;
	}
}

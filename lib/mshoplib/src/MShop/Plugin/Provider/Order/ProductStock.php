<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2020
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
	 */
	protected function checkStock( \Aimeos\MShop\Order\Item\Base\Iface $order ) : array
	{
		$codes = $types = $stockMap = [];

		foreach( $order->getProducts() as $orderProduct )
		{
			$codes[$orderProduct->getProductCode()][] = $orderProduct->getStockType();
			$types[$orderProduct->getStockType()] = null;
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$filter = $manager->filter()->add( ['product.code' => array_keys( $codes )] )->slice( 0, count( $codes ) );

		foreach( $manager->search( $filter, ['stock' => array_keys( $types )] ) as $prodItem )
		{
			foreach( $prodItem->getStockItems( $codes[$prodItem->getCode()] ?? [] ) as $stockItem ) {
				$stockMap[$prodItem->getCode()][$stockItem->getType()] = $stockItem;
			}
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
			$type = $orderProduct->getStockType();
			$code = $orderProduct->getProductCode();

			if( isset( $stockMap[$code][$type] ) )
			{
				$orderProduct->setTimeFrame( $stockMap[$code][$type]->getTimeFrame() );

				if( ( $stocklevel = $stockMap[$code][$type]->getStockLevel() ) === null ) {
					continue;
				}

				if( $stocklevel >= $orderProduct->getQuantity() )
				{
					$stock = $stockMap[$code][$type]->getStockLevel() - $orderProduct->getQuantity();
					$stockMap[$code][$type]->setStockLevel( $stock );
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

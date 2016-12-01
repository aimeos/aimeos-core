<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the products in a basket for sufficient stocklevel
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductStock
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( \Aimeos\MW\Observer\Publisher\Iface $order, $action, $value = null )
	{
		if( !( $order instanceof \Aimeos\MShop\Order\Item\Base\Iface ) )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Object is not of required type "%1$s"' );
			throw new \Aimeos\MShop\Plugin\Exception( sprintf( $msg, '\Aimeos\MShop\Order\Item\Base\Iface' ) );
		}


		if( !( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) ) {
			return true;
		}

		return $this->checkStockAll( $order );
	}


	/**
	 * Checks if all products in the basket have enough stock
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket object
	 * @return bool True if the checks succeeded
	 * @throws \Aimeos\MShop\Plugin\Provider\Exception If one or more products are out of stock
	 */
	protected function checkStockAll( \Aimeos\MShop\Order\Item\Base\Iface $order )
	{
		$productIds = $stockTypes = $stockMap = array();


		foreach( $order->getProducts() as $orderProductItem )
		{
			$productIds[] = $orderProductItem->getProductId();
			$stockTypes[] = $orderProductItem->getStockType();
		}

		$stockItems = $this->getStockItems( $productIds, $stockTypes );

		foreach( $stockItems as $stockItem ) {
			$stockMap[ $stockItem->getParentId() ][ $stockItem->getType() ] = $stockItem->getStocklevel();
		}

		$outOfStock = $this->getOutOfStock( $order, $stockMap );

		if( count( $outOfStock ) > 0 )
		{
			$code = array( 'product' => $outOfStock );
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Products out of stock' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return true;
	}


	/**
	 * Returns all product positions that have not enough stock
	 * Adapts the quantity of ordered products where not enough stock is available
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket object
	 * @param array $stockMap Multi-dimensional associative list of product IDs / stock types / stock levels
	 * @return array Associative list of product positions in the basket as keys and the out-of-stock code as values
	 */
	protected function getOutOfStock( \Aimeos\MShop\Order\Item\Base\Iface $order, array $stockMap )
	{
		$outOfStock = array();

		foreach( $order->getProducts() as $position => $orderProductItem )
		{
			if( !isset( $stockMap[ $orderProductItem->getProductId() ] )
				|| !array_key_exists( $orderProductItem->getStockType(), $stockMap[ $orderProductItem->getProductId() ] )
			) {
				$outOfStock[ $position ] = 'stock.notenough';
				continue;
			}

			$stocklevel = $stockMap[ $orderProductItem->getProductId() ][ $orderProductItem->getStockType() ];

			if( $stocklevel !== null && $stocklevel < $orderProductItem->getQuantity() )
			{
				$orderProductItem->setQuantity( $stocklevel );
				$outOfStock[ $position ] = 'stock.notenough';
			}
		}

		return $outOfStock;
	}


	/**
	 * Returns the stock items for the given product IDs and stock types
	 *
	 * @param array|string $productIds Unique product ID or list of product IDs
	 * @param array|string $types Unique stock types to limit the stock items
	 * @return array Associative list of stock item IDs as keys and items implementing \Aimeos\MShop\Product\Item\Stock\Iface as values
	 */
	protected function getStockItems( $productIds, $types )
	{
		$stockManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product/stock' );

		$search = $stockManager->createSearch();
		$expr = array(
			$search->compare( '==', 'product.stock.parentid', $productIds ),
			$search->compare( '==', 'product.stock.type.code', $types ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $stockManager->searchItems( $search );
	}
}

<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2016
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

		if( is_integer( $value ) && ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) === 0 ) {
			return true;
		}

		if( ( $outOfStock = $this->checkStock( $order ) ) !== [] )
		{
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Products out of stock' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, array( 'product' => $outOfStock ) );
		}

		return true;
	}


	/**
	 * Checks if all products in the basket have enough stock
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $order Shop basket object
	 * @return array Associative list of basket product positions as keys and the error codes as values
	 */
	protected function checkStock( \Aimeos\MShop\Order\Item\Base\Iface $order )
	{
		$productCodes = $stockTypes = $stockMap = [];

		foreach( $order->getProducts() as $orderProductItem )
		{
			$productCodes[] = $orderProductItem->getProductCode();
			$stockTypes[] = $orderProductItem->getStockType();
		}

		foreach( $this->getStockItems( $productCodes, $stockTypes ) as $stockItem ) {
			$stockMap[ $stockItem->getProductCode() ][ $stockItem->getType() ] = $stockItem->getStocklevel();
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
	protected function checkStockLevels( \Aimeos\MShop\Order\Item\Base\Iface $order, array $stockMap )
	{
		$outOfStock = [];

		foreach( $order->getProducts() as $position => $orderProductItem )
		{
			$stocklevel = 0;

			if( isset( $stockMap[ $orderProductItem->getProductCode() ] )
				&& array_key_exists( $orderProductItem->getStockType(), $stockMap[ $orderProductItem->getProductCode() ] )
			) {
				$stocklevel = $stockMap[ $orderProductItem->getProductCode() ][ $orderProductItem->getStockType() ];
			}

			if( $stocklevel === null || $stocklevel >= $orderProductItem->getQuantity() ) {
				continue;
			}

			if( $stocklevel > 0 ) {
				$orderProductItem->setQuantity( $stocklevel ); // update quantity to actual stock level
			} else {
				$order->deleteProduct( $position );
			}

			$outOfStock[$position] = 'stock.notenough';
		}

		return $outOfStock;
	}


	/**
	 * Returns the stock items for the given product codes and stock types
	 *
	 * @param array|string $codes Unique product code or list of product codes
	 * @param array|string $types Unique stock types to limit the stock items
	 * @return array Associative list of stock item IDs as keys and items implementing \Aimeos\MShop\Stock\Item\Iface as values
	 */
	protected function getStockItems( $codes, $types )
	{
		$stockManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'stock' );

		$search = $stockManager->createSearch();
		$expr = array(
			$search->compare( '==', 'stock.productcode', $codes ),
			$search->compare( '==', 'stock.type.code', $types ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $stockManager->searchItems( $search );
	}
}

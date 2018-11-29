<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Plugin
 */


namespace Aimeos\MShop\Plugin\Provider\Order;


/**
 * Checks the products in a basket for changed prices
 *
 * Notifies the customers if a price of a product in the basket has changed in
 * the meantime. This plugin can handle the change from net to gross prices and
 * backwards if prices are recalculated for B2B or B2C customers. In these cases
 * the customer won't be notified.
 *
 * To trace the execution and interaction of the plugins, set the log level to DEBUG:
 *	madmin/log/manager/standard/loglevel = 7
 *
 * @package MShop
 * @subpackage Plugin
 */
class ProductPrice
	extends \Aimeos\MShop\Plugin\Provider\Factory\Base
	implements \Aimeos\MShop\Plugin\Provider\Iface, \Aimeos\MShop\Plugin\Provider\Factory\Iface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param \Aimeos\MW\Observer\Publisher\Iface $p Object implementing publisher interface
	 */
	public function register( \Aimeos\MW\Observer\Publisher\Iface $p )
	{
		$p->addListener( $this->getObject(), 'check.after' );
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
		if( ( $value & \Aimeos\MShop\Order\Item\Base\Base::PARTS_PRODUCT ) === 0 ) {
			return true;
		}

		\Aimeos\MW\Common\Base::checkClass( '\\Aimeos\\MShop\\Order\\Item\\Base\\Iface', $order );

		$attrIds = $prodCodes = $changedProducts = [];
		$orderProducts = $order->getProducts();

		foreach( $orderProducts as $pos => $item )
		{
			if( $item->getFlags() & \Aimeos\MShop\Order\Item\Base\Product\Base::FLAG_IMMUTABLE ) {
				unset( $orderProducts[$pos] );
			}

			$prodCodes[] = $item->getProductCode();

			foreach( $item->getAttributes() as $ordAttrItem )
			{
				if( ( $id = $ordAttrItem->getAttributeId() ) != '' ) {
					$attrIds[$id] = null;
				}
			}
		}


		$attributes = $this->getAttributes( array_keys( $attrIds ) );
		$prodMap = $this->getProducts( $prodCodes );


		foreach( $orderProducts as $pos => $orderProduct )
		{
			if( !isset( $prodMap[$orderProduct->getProductCode()] ) ) {
				continue; // Product isn't available or excluded
			}

			// fetch prices of articles/sub-products
			$refPrices = $prodMap[$orderProduct->getProductCode()]->getRefItems( 'price', 'default', 'default' );

			$orderPosPrice = $orderProduct->getPrice();
			$price = $this->getPrice( $orderProduct, $refPrices, $attributes, $pos );

			if( $orderPosPrice->getTaxFlag() === $price->getTaxFlag() && $orderPosPrice->compare( $price ) === false )
			{
				$orderProduct->setPrice( $price );

				$order->deleteProduct( $pos );
				$order->addProduct( $orderProduct, $pos );

				$changedProducts[$pos] = 'price.changed';
			}
		}

		if( count( $changedProducts ) > 0 )
		{
			$code = array( 'product' => $changedProducts );
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'Please have a look at the prices of the products in your basket' );
			throw new \Aimeos\MShop\Plugin\Provider\Exception( $msg, -1, null, $code );
		}

		return true;
	}


	/**
	 * Returns the attribute items for the given IDs.
	 *
	 * @param array $ids List of attribute IDs
	 * @return \Aimeos\MShop\Attribute\Item\Iface[] List of attribute items
	 */
	protected function getAttributes( array $ids )
	{
		if( empty( $ids ) ) {
			return [];
		}

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );

		$search = $attrManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'attribute.id', $ids ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		return $attrManager->searchItems( $search, array( 'price' ) );
	}


	/**
	 * Returns the product items for the given product codes.
	 *
	 * @param string[] $prodCodes Product codes
	 */
	protected function getProducts( array $prodCodes )
	{
		if( empty( $prodCodes ) ) {
			return [];
		}

		$attrManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'attribute' );
		$productManager = \Aimeos\MShop\Factory::createManager( $this->getContext(), 'product' );

		$attrId = $attrManager->findItem( 'custom', [], 'product', 'price' )->getId();

		$search = $productManager->createSearch( true )->setSlice( 0, count( $prodCodes ) );
		$func = $search->createFunction( 'product:has', ['attribute', 'custom', $attrId] );
		$expr = array(
			$search->compare( '==', 'product.code', $prodCodes ),
			$search->compare( '==', $func, null ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$products = $productManager->searchItems( $search, array( 'price' ) );

		$prodMap = [];

		foreach( $products as $item ) {
			$prodMap[$item->getCode()] = $item;
		}

		return $prodMap;
	}


	/**
	 * Returns the actual price for the given order product.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct Ordered product
	 * @param array $refPrices Prices associated to the original product
	 * @param \Aimeos\MShop\Attribute\Item\Iface[] $attributes Attribute items with prices
	 * @param integer $pos Position of the product in the basket
	 * @return \Aimeos\MShop\Price\Item\Iface Price item including the calculated price
	 */
	private function getPrice( \Aimeos\MShop\Order\Item\Base\Product\Iface $orderProduct, array $refPrices, array $attributes, $pos )
	{
		$context = $this->getContext();

		// fetch prices of selection/parent products
		if( empty( $refPrices ) )
		{
			$productManager = \Aimeos\MShop\Factory::createManager( $context, 'product' );
			$product = $productManager->getItem( $orderProduct->getProductId(), array( 'price' ) );
			$refPrices = $product->getRefItems( 'price', 'default', 'default' );
		}

		if( empty( $refPrices ) )
		{
			$pid = $orderProduct->getProductId();
			$pcode = $orderProduct->getProductCode();
			$codes = array( 'product' => array( $pos => 'product.price' ) );
			$msg = $this->getContext()->getI18n()->dt( 'mshop', 'No price for product ID "%1$s" or product code "%2$s" available' );

			throw new \Aimeos\MShop\Plugin\Provider\Exception( sprintf( $msg, $pid, $pcode ), -1, null, $codes );
		}

		$priceManager = \Aimeos\MShop\Factory::createManager( $context, 'price' );
		$price = clone $priceManager->getLowestPrice( $refPrices, $orderProduct->getQuantity() );

		// add prices of product attributes to compute the end price for comparison
		foreach( $orderProduct->getAttributes() as $orderAttribute )
		{
			$attrPrices = [];
			$attrId = $orderAttribute->getAttributeId();

			if( isset( $attributes[$attrId] ) ) {
				$attrPrices = $attributes[$attrId]->getRefItems( 'price', 'default', 'default' );
			}

			if( !empty( $attrPrices ) ) {
				$price->addItem( $priceManager->getLowestPrice( $attrPrices, $orderAttribute->getQuantity() ), $orderAttribute->getQuantity() );
			}
		}

		// reset product rebates like in the basket controller
		$price->setRebate( '0.00' );

		return $price;
	}
}

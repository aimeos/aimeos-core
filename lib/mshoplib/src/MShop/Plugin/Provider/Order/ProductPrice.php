<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks the products in a basket for changed prices.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductPrice
	extends MShop_Plugin_Provider_Factory_Base
	implements MShop_Plugin_Provider_Factory_Interface
{
	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'check.after' );
	}


	/**
	 * Receives a notification from a publisher object
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 * @throws MShop_Plugin_Provider_Exception if checks fail
	 * @return bool true if checks succeed
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Base::PARTS_PRODUCT ) ) {
			return true;
		}


		$attrIds = $prodCodes = $changedProducts = array();
		$orderProducts = $order->getProducts();

		foreach( $orderProducts as $pos => $item )
		{
			if( $item->getFlags() & MShop_Order_Item_Base_Product_Base::FLAG_IMMUTABLE ) {
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
			$refPrices = array();

			// fetch prices of articles/sub-products
			if( isset( $prodMap[$orderProduct->getProductCode()] ) ) {
				$refPrices = $prodMap[$orderProduct->getProductCode()]->getRefItems( 'price', 'default', 'default' );
			}

			$orderPosPrice = $orderProduct->getPrice();
			$price = $this->getPrice( $orderProduct, $refPrices, $attributes, $pos );

			if( $orderPosPrice->compare( $price ) === false )
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
			$msg = sprintf( 'Please have a look at the prices of the products in your basket' );
			throw new MShop_Plugin_Provider_Exception( $msg, -1, null, $code );
		}

		return true;
	}


	/**
	 * Returns the attribute items for the given IDs.
	 *
	 * @param array $ids List of attribute IDs
	 * @return MShop_Attribute_Item_Interface[] List of attribute items
	 */
	protected function getAttributes( array $ids )
	{
		if( empty( $ids ) ) {
			return array();
		}

		$attrManager = MShop_Factory::createManager( $this->getContext(), 'attribute' );

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
			return array();
		}

		$productManager = MShop_Factory::createManager( $this->getContext(), 'product' );

		$search = $productManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', $prodCodes ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$products = $productManager->searchItems( $search, array( 'price' ) );

		$prodMap = array();

		foreach( $products as $item ) {
			$prodMap[$item->getCode()] = $item;
		}

		return $prodMap;
	}


	/**
	 * Returns the actual price for the given order product.
	 *
	 * @param MShop_Order_Item_Base_Product_Interface $orderProduct Ordered product
	 * @param array $refPrices Prices associated to the original product
	 * @param MShop_Attribute_Item_Interface[] $attributes Attribute items with prices
	 * @param integer $pos Position of the product in the basket
	 * @return MShop_Price_Item_Interface Price item including the calculated price
	 */
	private function getPrice( MShop_Order_Item_Base_Product_Interface $orderProduct, array $refPrices, array $attributes, $pos )
	{
		$context = $this->getContext();

		// fetch prices of selection/parent products
		if( empty( $refPrices ) )
		{
			$productManager = MShop_Factory::createManager( $context, 'product' );
			$product = $productManager->getItem( $orderProduct->getProductId(), array( 'price' ) );
			$refPrices = $product->getRefItems( 'price', 'default', 'default' );
		}

		if( empty( $refPrices ) )
		{
			$pid = $orderProduct->getProductId();
			$pcode = $orderProduct->getProductCode();
			$codes = array( 'product' => array( $pos => 'product.price' ) );
			$msg = sprintf( 'No price for product ID "%1$s" or product code "%2$s" available', $pid, $pcode );

			throw new MShop_Plugin_Provider_Exception( $msg, -1, null, $codes );
		}

		$priceManager = MShop_Factory::createManager( $context, 'price' );
		$price = $priceManager->getLowestPrice( $refPrices, $orderProduct->getQuantity() );

		// add prices of product attributes to compute the end price for comparison
		foreach( $orderProduct->getAttributes() as $orderAttribute )
		{
			$attrPrices = array();
			$attrId = $orderAttribute->getAttributeId();

			if( isset( $attributes[$attrId] ) ) {
				$attrPrices = $attributes[$attrId]->getRefItems( 'price', 'default', 'default' );
			}

			if( !empty( $attrPrices ) ) {
				$price->addItem( $priceManager->getLowestPrice( $attrPrices, $orderProduct->getQuantity() ) );
			}
		}

		// reset product rebates like in the basket controller
		$price->setRebate( '0.00' );

		return $price;
	}
}
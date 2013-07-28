<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks the products in a basket for changed prices.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductPrice implements MShop_Plugin_Provider_Interface
{

	private $_item;
	private $_context;


	/**
	 * Initializes the plugin instance
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Plugin_Item_Interface $item Plugin item object
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Plugin_Item_Interface $item )
	{
		$this->_item = $item;
		$this->_context = $context;
	}


	/**
	 * Subscribes itself to a publisher
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'isComplete.after' );
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
		$this->_context->getLogger()->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) {
			return true;
		}


		$codes = $prodMap = $changedProducts = array();
		$orderProducts = $order->getProducts();

		foreach( $orderProducts as $pos => $item ) {
			$codes[] = $item->getProductCode();
		}


		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_context );
		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $productManager->createSearch( true );
		$expr = array(
			$search->compare( '==', 'product.code', $codes ),
			$search->getConditions(),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );

		$products = $productManager->searchItems( $search, array( 'price' ) );


		foreach( $products as $item ) {
			$prodMap[ $item->getCode() ] = $item;
		}

		foreach( $orderProducts as $pos => $orderProduct )
		{
			$refPrices = array();

			if( isset( $prodMap[ $orderProduct->getProductCode() ] ) ) {
				$refPrices = $prodMap[ $orderProduct->getProductCode() ]->getRefItems( 'price' );
			}

			if( empty( $refPrices ) )
			{
				$product = $productManager->getItem( $orderProduct->getProductId(), array( 'price' ) );
				$refPrices = $product->getRefItems( 'price' );

				if( empty( $refPrices ) )
				{
					$code = array( 'product' => array( $pos => 'product.price' ) );
					$str = 'No price for product ID "%1$s" or product code "%2$s" available';
					$msg = sprintf( $str, $orderProduct->getProductId(), $orderProduct->getProductCode() );
					throw new MShop_Plugin_Provider_Exception( $msg, -1, null, $code );
				}
			}

			$price = $priceManager->getLowestPrice( $refPrices, $orderProduct->getQuantity() );

			if( ( $orderProducts[$pos]->getPrice()->getValue() !== $price->getValue()
				|| $orderProducts[$pos]->getPrice()->getShipping() !== $price->getShipping()
				|| $orderProducts[$pos]->getPrice()->getTaxrate() !== $price->getTaxrate() )
				&& $orderProducts[$pos]->getFlags() !== MShop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE )
			{
				$orderProducts[$pos]->setPrice( $price );

				$order->deleteProduct( $pos );
				$order->addProduct( $orderProducts[$pos], $pos );

				$changedProducts[$pos] = 'price.changed';
			}
		}

		if ( count( $changedProducts ) > 0 )
		{
			$code = array( 'product' => $changedProducts );
			$msg = sprintf( 'The price of at least one product in the basket has changed in the meantime and was updated' );
			throw new MShop_Plugin_Provider_Exception( $msg, -1, null, $code );
		}

		return true;
	}
}
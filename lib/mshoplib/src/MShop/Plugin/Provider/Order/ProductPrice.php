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
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Order_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) {
			return true;
		}

		$orderProducts = $order->getProducts();

		$positions = array();
		foreach ( $orderProducts as $position => $pr ) {
			$positions[$pr->getProductId()] = $position;
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );
		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', array_keys( $positions ) ) );

		$priceManager = MShop_Price_Manager_Factory::createManager( $this->_context );

		$products = $productManager->searchItems( $search, array( 'price' ) );

		$changedProducts = array();
		foreach ( $products as $id => $product )
		{
			$referencePrices = $product->getRefItems('price');
			$price = $priceManager->getLowestPrice( $referencePrices, $orderProducts[$positions[$id]]->getQuantity() );

			if( ( $orderProducts[$positions[$id]]->getPrice()->getValue() !== $price->getValue()
				|| $orderProducts[$positions[$id]]->getPrice()->getShipping() !== $price->getShipping()
				|| $orderProducts[$positions[$id]]->getPrice()->getTaxrate() !== $price->getTaxrate() )
				&& $orderProducts[$positions[$id]]->getFlags() !== Mshop_Order_Item_Base_Product_Abstract::FLAG_IMMUTABLE )
			{
				$orderProducts[$positions[$id]]->setPrice( $price );

				$order->deleteProduct( $positions[$id] );
				$order->addProduct( $orderProducts[ $positions[$id] ], $positions[$id] );

				$changedProducts[$positions[$id]] = 'product.price';
			}
		}

		if ( count( $changedProducts ) > 0 )
		{
			$code = array( 'product' => $changedProducts );
			throw new MShop_Plugin_Provider_Exception( sprintf( 'The price of at least one product in the basket has changed in the meantime and was updated'), -1, null, $code );
		}

		return true;
	}
}
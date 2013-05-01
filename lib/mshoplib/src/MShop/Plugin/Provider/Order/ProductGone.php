<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id$
 */


/**
 * Checks the current availability of the products in a basket
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductGone implements MShop_Plugin_Provider_Interface
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
			throw new MShop_Plugin_Order_Exception(sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) {
			return true;
		}

		$config = $this->_item->getConfig();
		$this->_context->getLogger()->log(__METHOD__ . ':: config: ' . print_r( $config, true ), MW_Logger_Abstract::DEBUG);

		$productIds = array();
		foreach ( $order->getProducts() as $pr ) {
			$productIds[] = $pr->getProductId();
		}

		$productManager = MShop_Product_Manager_Factory::createManager( $this->_context );

		$search = $productManager->createSearch();
		$search->setConditions( $search->compare( '==', 'product.id', $productIds ) );
		$checkItems = $productManager->searchItems( $search );

		$notAvailable = array();
		$now = date( 'Y-m-d H-i-s' );
		foreach ( $order->getProducts() as $position => $orderProduct )
		{
			if ( !array_key_exists( $orderProduct->getProductId(), $checkItems ) )
			{
				$notAvailable[$position] = 'product.status';
				continue;
			}

			$product = $checkItems[$orderProduct->getProductId()];

			if ( $product->getStatus() <= 0 )
			{
				$notAvailable[$position] = 'product.status';
				continue;
			}

			$start = $product->getDateStart();
			$end = $product->getDateEnd();

			if ( ( ( $start !== null ) && ( $start >= $now) ) || ( ( $end !== null ) && ( $now > $end ) ) ) {
				$notAvailable[$position] = 'product.status';
			}
		}

		if ( count( $notAvailable ) > 0 )
		{
			$code = array( 'product' => $notAvailable );
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Products in basket not available' ), -1, null, $code );
		}

		return true;
	}
}
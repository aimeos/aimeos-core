<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Free shipping implementation if ordered product sum is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_BasketLimits implements MShop_Plugin_Provider_Interface
{
	private $_item = null;
	private $_context = null;


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

		if( $this->_context->getConfig()->get( 'mshop/plugin/provider/order/complete/disable', false ) )
		{
			$this->_context->getLogger()->log(__METHOD__ . ': Is disabled', MW_Logger_Abstract::DEBUG);
			return true;
		}

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception(sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) { return true; }


		$count = 0;
		$failures = array();
		$config = $this->_item->getConfig();
		$sum = MShop_Price_Manager_Factory::createManager( $this->_context )->createItem();

		foreach( $order->getProducts() as $product )
		{
			$sum->addItem( $product->getPrice(), $product->getQuantity());
			$count += $product->getQuantity();
		}

		if( ( isset( $config['min-value'] ) ) && ( $sum->getValue() + $sum->getRebate() < $config['min-value'] ) ) {
			$failures[] = 'limit.min-value';
		}

		if( ( isset( $config['min-products'] ) ) && ( $count < $config['min-products'] ) ) {
			$failures[] = 'limit.min-products';
		}

		if( count( $failures ) > 0 )
		{
			$msg = sprintf( 'Minimum value or minimum amount of products in basket is not reached' );
			throw new MShop_Plugin_Provider_Exception( $msg, -1, null, array( 'basket' => $failures ) );
		}

		return true;
	}
}

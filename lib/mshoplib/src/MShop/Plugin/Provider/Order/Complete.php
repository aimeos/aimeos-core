<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: Complete.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Free shipping implementation if ordered product sum is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_Complete implements MShop_Plugin_Provider_Interface
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
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );


		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) { return true; }

		if( $this->_context->getConfig()->get( 'mshop/plugin/provider/order/complete/disable', false ) )
		{
			$this->_context->getLogger()->log( __METHOD__ . ': Is disabled', MW_Logger_Abstract::INFO );
			return true;
		}


		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			$msg = 'Received notification from "%1$s" which doesn\'t implement "%2$s"';
			throw new MShop_Plugin_Exception(sprintf( $msg, get_class( $order ), $class ) );
		}


		$config = $this->_item->getConfig();
		
		$currencyid = $this->_context->getLocale()->getCurrencyId();

		if( isset( $config['min-value'][$currencyid] ) )
		{
			$sum = MShop_Price_Manager_Factory::createManager( $this->_context )->createItem();

			foreach( $order->getProducts() as $product ) {
				$sum->addItem( $product->getPrice(), $product->getQuantity());
			}
			
			if( $sum->getValue() + $sum->getRebate() > $config['min-value'][$currencyid] ) {
				return true;
			}
		}

		if( isset( $config['min-products'] ) )
		{
			$count = 0;

			foreach( $order->getProducts() as $product ) {
				$count += $product->getQuantity();
			}

			if( $count >= $config['min-products'] ) {
				return true;
			}
		}

		return false;
	}

}

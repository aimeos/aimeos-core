<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: Shipping.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Free shipping implementation if ordered product sum is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_Shipping implements MShop_Plugin_Provider_Interface
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
		$p->addListener( $this, 'addProduct.after' );
		$p->addListener( $this, 'deleteProduct.after' );
		$p->addListener( $this, 'setService.after' );
		$p->addListener( $this, 'addCoupon.after' );
		$p->addListener( $this, 'deleteCoupon.after' );
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
		$this->_context->getLogger()->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->_item->getConfig();
		if( !isset( $config['threshold'] ) ) { return true; }

		try {
			$delivery = $order->getService( 'delivery' );
		} catch( MShop_Order_Exception $oe ) {
			// no delivery item available yet
			return true;
		}

		$price = $delivery->getPrice();
		$currency = $price->getCurrencyId();

		if( !isset( $config['threshold'][$currency] ) )
		{
			$this->_context->getLogger()->log( sprintf( 'Threshold for free shipping for currency ID "%1$s" not available.', $currency ), MW_Logger_Abstract::WARN );
			return true;
		}

		$sum = MShop_Price_Manager_Factory::createManager( $this->_context )->createItem();

		foreach( $order->getProducts() as $product ) {
			$sum->addItem( $product->getPrice(), $product->getQuantity() );
		}

		if( $sum->getValue() + $sum->getRebate() > $config['threshold'][$currency] && $price->getShipping() > '0.00' )
		{
			$price->setRebate( $price->getShipping() );
			$price->setShipping( '0.00' );
		}
		else if( $sum->getValue() + $sum->getRebate() < $config['threshold'][$currency] && $price->getRebate() > '0.00' )
		{
			$price->setShipping( $price->getRebate() );
			$price->setRebate( '0.00' );
		}

		return true;
	}
}
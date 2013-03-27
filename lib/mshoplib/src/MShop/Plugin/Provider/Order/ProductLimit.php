<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 * @version $Id: ProductLimit.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Free shipping implementation if ordered product sum is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductLimit implements MShop_Plugin_Provider_Interface
{
	private $_item;
	private $_context;

	/**
	 * Initializes the plugin instance.
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
	 * Subscribes itself to a publisher.
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.before' );
	}


	/**
	 * Receives a notification from a publisher object.
	 *
	 * @param MW_Observer_Publisher_Interface $order Shop basket instance implementing publisher interface
	 * @param string $action Name of the action to listen for
	 * @param mixed $value Object or value changed in publisher
	 */
	public function update( MW_Observer_Publisher_Interface $order, $action, $value = null )
	{
		$this->_context->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			throw new MShop_Plugin_Exception( sprintf( 'Received notification from "%1$s" which doesn\'t implement "%2$s"', get_class( $order ), $class ) );
		}

		$class = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $value instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->_item->getConfig();


		if( isset( $config['single-number-max'] ) && $value->getQuantity() > (int) $config['single-number-max'] ) {
			throw new MShop_Plugin_Exception( sprintf( 'Number of single product exceeds given limit' ) );
		}


		if( isset( $config['total-number-max'] ) )
		{
			$total = $value->getQuantity();

			foreach( $order->getProducts() as $product ) {
				$total += $product->getQuantity();
			}

			if( $total > (int) $config['total-number-max'] ) {
				throw new MShop_Plugin_Exception( sprintf( 'Total number of product exceeds given limit' ) );
			}
		}


		$currencyId = $value->getPrice()->getCurrencyId();


		if( isset( $config['single-value-max'][$currencyId] )
			&& $value->getPrice()->getValue() * $value->getQuantity() > (float) $config['single-value-max'][$currencyId] ) {
			throw new MShop_Plugin_Exception( sprintf( 'Value of single product exceeds given limit' ) );
		}


		if( isset( $config['total-value-max'][$currencyId] ) )
		{
			$price = clone $value->getPrice();
			$price->setValue( $price->getValue() * $value->getQuantity() );

			foreach( $order->getProducts() as $product ) {
				$price->addItem( $product->getPrice(), $product->getQuantity() );
			}

			if( (float) $price->getValue() > (float) $config['total-value-max'][$currencyId] ) {
				throw new MShop_Plugin_Exception( sprintf( 'Total value of product exceeds given limit' ) );
			}
		}

		return true;
	}
}

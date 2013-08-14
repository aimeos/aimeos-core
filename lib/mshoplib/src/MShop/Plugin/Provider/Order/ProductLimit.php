<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Free shipping implementation if ordered product sum is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductLimit
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Interface
{


	/**
	 * Subscribes itself to a publisher.
	 *
	 * @param MW_Observer_Publisher_Interface $p Object implementing publisher interface
	 */
	public function register( MW_Observer_Publisher_Interface $p )
	{
		$p->addListener( $this, 'addProduct.after' );
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
		$this->_getContext()->getLogger()->log( __METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG );

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$class = 'MShop_Order_Item_Base_Product_Interface';
		if( !( $value instanceof $class ) ) {
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->_getItem()->getConfig();


		if( isset( $config['single-number-max'] ) && $value->getQuantity() > (int) $config['single-number-max'] )
		{
			$msg = sprintf( 'The maximum product quantity is %1$d', (int) $config['single-number-max'] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}


		if( isset( $config['total-number-max'] ) )
		{
			$total = $value->getQuantity();

			foreach( $order->getProducts() as $product ) {
				$total += $product->getQuantity();
			}

			if( $total > (int) $config['total-number-max'] )
			{
				$msg = sprintf( 'The maximum quantity of all products is %1$d', (int) $config['total-number-max'] );
				throw new MShop_Plugin_Provider_Exception( $msg );
			}
		}


		$currencyId = $value->getPrice()->getCurrencyId();


		if( isset( $config['single-value-max'][$currencyId] )
			&& $value->getPrice()->getValue() * $value->getQuantity() > (float) $config['single-value-max'][$currencyId]
		) {
			$msg = sprintf( 'The maximum product value is %1$s', $config['single-value-max'][$currencyId] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}


		if( isset( $config['total-value-max'][$currencyId] ) )
		{
			$price = clone $value->getPrice();
			$price->setValue( $price->getValue() * $value->getQuantity() );

			foreach( $order->getProducts() as $product ) {
				$price->addItem( $product->getPrice(), $product->getQuantity() );
			}

			if( (float) $price->getValue() > (float) $config['total-value-max'][$currencyId] )
			{
				$msg = sprintf( 'The maximum value of all products is %1$s', $config['total-value-max'][$currencyId] );
				throw new MShop_Plugin_Provider_Exception( $msg );
			}
		}

		return true;
	}
}

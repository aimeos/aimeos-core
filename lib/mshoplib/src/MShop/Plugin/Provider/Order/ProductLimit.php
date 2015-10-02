<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Product limit implementation if count or sum of a single or of all products in an order exceeds given limit
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_ProductLimit
	extends MShop_Plugin_Provider_Factory_Abstract
	implements MShop_Plugin_Provider_Factory_Interface
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
		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Provider_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$this->_checkWithoutCurrency( $order, $value );
		$this->_checkWithCurrency( $order, $value );

		return true;
	}


	/**
	 * Checks for the product limits when the configuration doesn't contain limits per currency.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket object
	 * @param MShop_Order_Item_Base_Product_Interface $value Order product item
	 * @throws MShop_Plugin_Provider_Exception If one limit is exceeded
	 */
	protected function _checkWithoutCurrency( MShop_Order_Item_Base_Interface $order,
		MShop_Order_Item_Base_Product_Interface $value )
	{
		$config = $this->getItemBase()->getConfig();

		if( isset( $config['single-number-max'] )
			&& !is_array( $config['single-number-max'] )
			&& $value->getQuantity() > (int) $config['single-number-max']
		) {
			$msg = sprintf( 'The maximum product quantity is %1$d', (int) $config['single-number-max'] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}


		if( isset( $config['total-number-max'] ) && !is_array( $config['total-number-max'] ) )
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
	}


	/**
	 * Checks for the product limits when the configuration contains limits per currency.
	 *
	 * @param MShop_Order_Item_Base_Interface $order Basket object
	 * @param MShop_Order_Item_Base_Product_Interface $value Order product item
	 * @throws MShop_Plugin_Provider_Exception If one limit is exceeded
	 */
	protected function _checkWithCurrency( MShop_Order_Item_Base_Interface $order,
		MShop_Order_Item_Base_Product_Interface $value )
	{
		$config = $this->getItemBase()->getConfig();
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
	}
}

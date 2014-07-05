<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Checks if ordered product sum and count of products is above a certain value.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_BasketLimits
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Interface
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
		$context = $this->_getContext();
		$logger = $context->getLogger();

		$logger->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		if( $context->getConfig()->get( 'mshop/plugin/provider/order/complete/disable', false ) )
		{
			$logger->log(__METHOD__ . ': Is disabled', MW_Logger_Abstract::DEBUG);
			return true;
		}

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Provider_Exception(sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		if( !( $value & MShop_Order_Item_Base_Abstract::PARTS_PRODUCT ) ) { return true; }


		$count = 0;
		$failures = array();
		$config = $this->_getItem()->getConfig();
		$sum = MShop_Factory::createManager( $context, 'price' )->createItem();

		foreach( $order->getProducts() as $product )
		{
			$sum->addItem( $product->getPrice(), $product->getQuantity());
			$count += $product->getQuantity();
		}

		$currencyId = $sum->getCurrencyId();

		if( ( isset( $config['min-value'][$currencyId] ) ) && ( $sum->getValue() + $sum->getRebate() < $config['min-value'][$currencyId] ) )
		{
			$msg = sprintf( 'The minimum basket value of %1$s isn\'t reached', $config['min-value'][$currencyId] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}

		if( ( isset( $config['max-value'][$currencyId] ) ) && ( $sum->getValue() + $sum->getRebate() > $config['max-value'][$currencyId] ) )
		{
			$msg = sprintf( 'The maximum basket value of %1$s is exceeded', $config['max-value'][$currencyId] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}

		if( ( isset( $config['min-products'] ) ) && ( $count < $config['min-products'] ) )
		{
			$msg = sprintf( 'The minimum product quantity of %1$d isn\'t reached', $config['min-products'] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}

		if( ( isset( $config['max-products'] ) ) && ( $count > $config['max-products'] ) )
		{
			$msg = sprintf( 'The maximum product quantity of %1$d is exceeded', $config['max-products'] );
			throw new MShop_Plugin_Provider_Exception( $msg );
		}

		return true;
	}
}

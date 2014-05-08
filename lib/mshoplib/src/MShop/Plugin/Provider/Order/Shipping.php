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
class MShop_Plugin_Provider_Order_Shipping
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
		$context = $this->_getContext();
		$logger = $context->getLogger();

		$logger->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) ) {
			throw new MShop_Plugin_Exception( sprintf( 'Object is not of required type "%1$s"', $class ) );
		}

		$config = $this->_getItem()->getConfig();
		if( !isset( $config['threshold'] ) ) { return true; }

		try {
			$delivery = $order->getService( 'delivery' );
		} catch( MShop_Order_Exception $oe ) {
			// no delivery item available yet
			return true;
		}

		$price = $delivery->getPrice();
		$currency = $price->getCurrencyId();

		if( !isset( $config['threshold'][$currency] ) ) {
			return true;
		}

		$sum = MShop_Price_Manager_Factory::createManager( $context )->createItem();

		foreach( $order->getProducts() as $product ) {
			$sum->addItem( $product->getPrice(), $product->getQuantity() );
		}

		if( $sum->getValue() + $sum->getRebate() >= $config['threshold'][$currency] && $price->getCosts() > '0.00' )
		{
			$price->setRebate( $price->getCosts() );
			$price->setCosts( '0.00' );
		}
		else if( $sum->getValue() + $sum->getRebate() < $config['threshold'][$currency] && $price->getRebate() > '0.00' )
		{
			$price->setCosts( $price->getRebate() );
			$price->setRebate( '0.00' );
		}

		return true;
	}
}
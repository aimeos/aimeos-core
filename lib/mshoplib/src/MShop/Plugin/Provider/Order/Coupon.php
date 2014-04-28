<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @package MShop
 * @subpackage Plugin
 * @version $Id: Coupon.php 37 2012-08-08 17:37:40Z fblasel $
 */


/**
 * Update percent rebate value on change.
 *
 * @package MShop
 * @subpackage Plugin
 */
class MShop_Plugin_Provider_Order_Coupon
	extends MShop_Plugin_Provider_Order_Abstract
	implements MShop_Plugin_Provider_Interface
{
	protected static $_lock = false;


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

		$context->getLogger()->log(__METHOD__ . ': event=' . $action, MW_Logger_Abstract::DEBUG);

		if ( self::$_lock === true ) { return; }

		self::$_lock = true;

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			$msg = 'Received notification from "%1$s" which doesn\'t implement "%2$s"';
			throw new MShop_Plugin_Exception(sprintf($msg, get_class($order), $class));
		}

		$couponManager = MShop_Coupon_Manager_Factory::createManager( $context );
		$searchObj = $couponManager->createSearch();
		foreach( $order->getCoupons() as $code => $products )
		{
			$search = clone $searchObj;
			$search->setConditions( $search->compare( '==', 'coupon.code.code', $code ) );
			$results = $couponManager->searchItems( $search );

			if( ( $couponItem = reset( $results ) ) === false )
			{
				$msg = 'no item found with code "%1$s" in method: "%2$s"';
				throw new MShop_Plugin_Exception( sprintf($msg, $code, __METHOD__) );
			}

			$couponProvider = $couponManager->getProvider($couponItem, $code);
			$couponProvider->updateCoupon( $order );
		}

		self::$_lock = false;

		return true;
	}

}

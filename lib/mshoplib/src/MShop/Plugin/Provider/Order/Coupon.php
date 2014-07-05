<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Plugin
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

		$class = 'MShop_Order_Item_Base_Interface';
		if( !( $order instanceof $class ) )
		{
			$msg = 'Received notification from "%1$s" which doesn\'t implement "%2$s"';
			throw new MShop_Plugin_Exception(sprintf($msg, get_class($order), $class));
		}

		if( self::$_lock === false )
		{
			self::$_lock = true;

			$couponManager = MShop_Factory::createManager( $context, 'coupon' );
			$search = $couponManager->createSearch();

			foreach( $order->getCoupons() as $code => $products )
			{
				$search->setConditions( $search->compare( '==', 'coupon.code.code', $code ) );
				$results = $couponManager->searchItems( $search );

				if( ( $couponItem = reset( $results ) ) !== false )
				{
					$couponProvider = $couponManager->getProvider( $couponItem, $code );
					$couponProvider->updateCoupon( $order );
				}
			}

			self::$_lock = false;
		}

		return true;
	}

}

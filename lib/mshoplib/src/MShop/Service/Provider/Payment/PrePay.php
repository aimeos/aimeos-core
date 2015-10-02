<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Payment provider for pre-paid orders.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Payment_PrePay
	extends MShop_Service_Provider_Payment_Abstract
	implements MShop_Service_Provider_Payment_Interface
{
	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 */
	public function cancel( MShop_Order_Item_Interface $order )
	{
		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_CANCELED );
		$this->saveOrder( $order );
	}


	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return MShop_Common_Item_Helper_Form_Default Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Interface $order, array $params = array() )
	{
		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_PENDING );
		$this->saveOrder( $order );

		return parent::process( $order, $params );
	}


	/**
	 * Checks what features the payment provider implements.
	 *
	 * @param integer $what Constant from abstract class
	 * @return boolean True if feature is available in the payment provider, false if not
	 */
	public function isImplemented( $what )
	{
		return $what === MShop_Service_Provider_Payment_Abstract::FEAT_CANCEL;
	}
}
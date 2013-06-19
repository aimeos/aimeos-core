<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Payment provider for post-paid orders.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Payment_PostPay
extends MShop_Service_Provider_Payment_Abstract
implements MShop_Service_Provider_Payment_Interface
{
	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object
	 * @return MShop_Common_Item_Helper_Form_Interface|null Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider) or null to redirect directly to the confirmation page
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setPaymentStatus( MShop_Order_Item_Abstract::PAY_AUTHORIZED );
	}
}
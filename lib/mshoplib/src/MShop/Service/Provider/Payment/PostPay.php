<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	extends MShop_Service_Provider_Payment_Base
	implements MShop_Service_Provider_Payment_Iface
{
	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Iface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return MShop_Common_Item_Helper_Form_Default Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Iface $order, array $params = array() )
	{
		$order->setPaymentStatus( MShop_Order_Item_Base::PAY_AUTHORIZED );
		$this->saveOrder( $order );

		return parent::process( $order, $params );
	}
}
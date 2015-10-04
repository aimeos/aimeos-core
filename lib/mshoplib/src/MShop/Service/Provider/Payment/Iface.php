<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Interface with specific methods for payment providers.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Provider_Payment_Iface extends MShop_Service_Provider_Factory_Iface
{
	/**
	 * Cancels the authorization for the given order if supported.
	 *
	 * @param MShop_Order_Item_Iface $order Order invoice object
	 * @return void
	 */
	public function cancel( MShop_Order_Item_Iface $order );

	/**
	 * Captures the money later on request for the given order if supported.
	 *
	 * @param MShop_Order_Item_Iface $order Order invoice object
	 * @return void
	 */
	public function capture( MShop_Order_Item_Iface $order );

	/**
	 * Tries to get an authorization or captures the money immediately for the given order if capturing the money
	 * separately isn't supported or not configured by the shop owner.
	 *
	 * @param MShop_Order_Item_Iface $order Order invoice object
	 * @param array $params Request parameter if available
	 * @return MShop_Common_Item_Helper_Form_Standard Form object with URL, action and parameters to redirect to
	 * 	(e.g. to an external server of the payment provider or to a local success page)
	 */
	public function process( MShop_Order_Item_Iface $order, array $params = array() );

	/**
	 * Refunds the money for the given order if supported.
	 *
	 * @param MShop_Order_Item_Iface $order Order invoice object
	 * @return void
	 */
	public function refund( MShop_Order_Item_Iface $order );
}

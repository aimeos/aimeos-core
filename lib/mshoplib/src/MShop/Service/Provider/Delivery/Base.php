<?php


/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Abstract class for all delivery provider implementations.
 *
 * @package MShop
 * @subpackage Service
 */
abstract class MShop_Service_Provider_Delivery_Base
extends MShop_Service_Provider_Base
implements MShop_Service_Provider_Delivery_Iface
{
	/**
	 * Feature constant if querying for status updates for an order is supported.
	 */
	const FEAT_QUERY = 1;

	const ERR_OK = 0;
	const ERR_TEMP = 1;
	const ERR_XML = 10;
	const ERR_SCHEMA = 11;


	/**
	 * Sets the delivery attributes in the given service.
	 *
	 * @param MShop_Order_Item_Base_Service_Iface $orderServiceItem Order service item that will be added to the basket
	 * @param array $attributes Attribute key/value pairs entered by the customer during the checkout process
	 */
	public function setConfigFE( MShop_Order_Item_Base_Service_Iface $orderServiceItem, array $attributes )
	{
		$this->setAttributes( $orderServiceItem, $attributes, 'delivery' );
	}
}
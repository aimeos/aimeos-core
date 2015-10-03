<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Manual delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Delivery_Manual
	extends MShop_Service_Provider_Delivery_Base
	implements MShop_Service_Provider_Delivery_Interface
{
	/**
	 * Updates the delivery status.
	 *
	 * @param MShop_Order_Item_Interface $order Order instance
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setDeliveryStatus( MShop_Order_Item_Base::STAT_PROGRESS );
	}

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id$
 */


/**
 * Manual delivery provider implementation.
 *
 * @package MShop
 * @subpackage Service
 */
class MShop_Service_Provider_Delivery_Manual
	extends MShop_Service_Provider_Delivery_Abstract
	implements MShop_Service_Provider_Delivery_Interface
{
	/**
	 * Updates the delivery status.
	 *
	 * @param MShop_Order_Item_Interface $order Order instance
	 */
	public function process( MShop_Order_Item_Interface $order )
	{
		$order->setDeliveryStatus( MShop_Order_Item_Abstract::STAT_PROGRESS );
	}

}

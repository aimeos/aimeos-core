<?php 

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */

/**
 * Interface with specific methods for delivery providers.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Provider_Delivery_Interface extends MShop_Service_Provider_Factory_Interface
{
	/**
	 * Sends the order details to the ERP system for further processing.
	 *
	 * @param MShop_Order_Item_Interface $order Order invoice object to process
	 */
	public function process( MShop_Order_Item_Interface $order );
}
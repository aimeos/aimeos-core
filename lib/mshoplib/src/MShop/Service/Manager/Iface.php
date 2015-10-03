<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Common interface for all service manager implementations.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Manager_Iface
	extends MShop_Common_Manager_Factory_Iface
{
	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param MShop_Service_Item_Iface $item Delivery or payment service item object
	 * @return MShop_Service_Provider_Iface Returns a service provider implementing MShop_Service_Provider_Iface
	 * @throws MShop_Service_Exception If provider couldn't be found
	 */
	public function getProvider( MShop_Service_Item_Iface $item );
}

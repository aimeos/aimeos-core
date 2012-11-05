<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Common interface for all service manager implementations.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Manager_Interface
	extends MShop_Common_Manager_Factory_Interface
{
	/**
	 * Returns the service provider which is responsible for the service item.
	 *
	 * @param MShop_Service_Item_Interface $item Delivery or payment service item object
	 * @return MShop_Service_Provider_Interface Returns a service provider implementing MShop_Service_Provider_Interface
	 * @throws MShop_Service_Exception If provider couldn't be found
	 */
	public function getProvider( MShop_Service_Item_Interface $item );
}

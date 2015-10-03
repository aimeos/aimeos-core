<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Service
 */


/**
 * Service decorator interface.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Provider_Decorator_Iface
	extends MShop_Service_Provider_Iface
{
	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Service_Item_Iface $serviceItem Service item with configuration for the provider
	 * @param MShop_Service_Provider_Iface $provider Service provider or decorator
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context,
		MShop_Service_Item_Iface $serviceItem, MShop_Service_Provider_Iface $provider );
}
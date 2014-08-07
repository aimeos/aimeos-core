<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Service
 */


/**
 * Service decorator interface.
 *
 * @package MShop
 * @subpackage Service
 */
interface MShop_Service_Provider_Decorator_Interface
	extends MShop_Service_Provider_Interface
{
	/**
	 * Initializes a new service provider object using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Service_Item_Interface $serviceItem Service item with configuration for the provider
	 * @param MShop_Service_Provider_Interface $provider Service provider or decorator
	 * @return void
	 */
	public function __construct(MShop_Context_Item_Interface $context,
		MShop_Service_Item_Interface $serviceItem, MShop_Service_Provider_Interface $provider );
}
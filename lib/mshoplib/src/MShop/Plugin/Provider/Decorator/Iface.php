<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Plugin decorator interface for dealing with run-time loadable extenstions.
 *
 * @package MShop
 * @subpackage Plugin
 */
interface MShop_Plugin_Provider_Decorator_Iface extends MShop_Plugin_Provider_Iface
{
	/**
	 * Initializes the plugin decorator object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Plugin_Item_Iface $item Plugin item object
	 * @param MShop_Plugin_Provider_Iface $item Plugin item object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Plugin_Item_Iface $item,
		MShop_Plugin_Provider_Iface $provider );
}

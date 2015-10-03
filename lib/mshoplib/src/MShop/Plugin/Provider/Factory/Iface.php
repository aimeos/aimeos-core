<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Plugin
 */


/**
 * Order plugin interface for dealing with run-time loadable extenstions.
 *
 * @package MShop
 * @subpackage Plugin
 */
interface MShop_Plugin_Provider_Factory_Iface extends MShop_Plugin_Provider_Iface
{
	/**
	 * Initializes the plugin object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Plugin_Item_Iface $item Plugin item object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Plugin_Item_Iface $item );
}

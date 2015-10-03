<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Factory_Iface
{
	/**
	 *	Creates a manager object.
	 *
	 * @param MShop_Context_Item_Iface $context Context instance with necessary objects
	 * @param string $name Manager name (from configuration or "Default" if null)
	 * @return MShop_Common_Manager_Iface New manager object
	 */
	public static function createManager( MShop_Context_Item_Iface $context, $name = null );
}

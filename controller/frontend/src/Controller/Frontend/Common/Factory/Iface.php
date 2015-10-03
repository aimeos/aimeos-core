<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Common_Factory_Iface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return Controller_Frontend_Common_Iface Controller object
	 */
	public static function createController( MShop_Context_Item_Iface $context, $name = null );
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Controller_Jobs_Common_Factory_Iface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 * @param Aimeos $aimeos Aimeos object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return Controller_Jobs_Iface Controller object
	 */
	public static function createController( MShop_Context_Item_Iface $context, Aimeos $aimeos, $name = null );
}
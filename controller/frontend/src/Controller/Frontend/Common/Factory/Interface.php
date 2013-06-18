<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Controller factory interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Common_Factory_Interface
{
	/**
	 * Creates a new controller based on the name.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 * @param string|null $name Name of the controller implementation (Default if null)
	 * @return Controller Interface
	 */
	public static function createController( MShop_Context_Item_Interface $context, $name = null );
}

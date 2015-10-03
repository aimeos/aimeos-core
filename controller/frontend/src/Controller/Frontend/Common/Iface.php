<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Frontend
 */


/**
 * Frontend controller interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Common_Iface
	extends Controller_Frontend_Iface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Iface $context MShop context object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context );

}

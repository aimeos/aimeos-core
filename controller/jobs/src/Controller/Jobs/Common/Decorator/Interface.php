<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage Jobs
 */
interface Controller_Jobs_Common_Decorator_Interface
	extends Controller_Jobs_Interface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param Aimeos $aimeos Aimeos object
	 * @param Controller_Jobs_Interface $controller Controller object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context, Aimeos $aimeos,
		Controller_Jobs_Interface $controller );
}
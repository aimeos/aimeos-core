<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Common_Decorator_Interface
	extends Controller_ExtJS_Interface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param Controller_ExtJS_Interface $controller Controller object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_ExtJS_Interface $controller );
}
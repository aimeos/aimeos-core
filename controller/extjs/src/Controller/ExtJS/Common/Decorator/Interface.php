<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_ExtJS_Interface $controller );
}
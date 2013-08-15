<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
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
	 * @param Controller_Jobs_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_Jobs_Interface $controller );
}
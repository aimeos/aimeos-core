<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Interface.php 866 2012-06-28 16:14:02Z fblasel $
 */


/**
 * Decorator interface for controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Common_Decorator_Interface
	extends Controller_Frontend_Interface
{
	/**
	 * Initializes a new controller decorator object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param Controller_Frontend_Interface $controller Controller object
	 */
	public function __construct( MShop_Context_Item_Interface $context, Controller_Frontend_Interface $controller );
}

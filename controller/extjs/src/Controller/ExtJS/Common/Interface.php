<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 * @version $Id: Interface.php 14265 2011-12-11 16:57:33Z nsendetzky $
 */


/**
 * ExtJS controller interface.
 *
 * @package Controller
 * @subpackage ExtJS
 */
interface Controller_ExtJS_Common_Interface
	extends Controller_ExtJS_Interface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context );

}

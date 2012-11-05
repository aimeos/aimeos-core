<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Frontend
 * @version $Id: Interface.php 866 2012-06-28 16:14:02Z fblasel $
 */


/**
 * Frontend controller interface.
 *
 * @package Controller
 * @subpackage Frontend
 */
interface Controller_Frontend_Common_Interface
	extends Controller_Frontend_Interface
{
	/**
	 * Initializes the controller.
	 *
	 * @param MShop_Context_Item_Interface $context MShop context object
	 */
	public function __construct( MShop_Context_Item_Interface $context );

}

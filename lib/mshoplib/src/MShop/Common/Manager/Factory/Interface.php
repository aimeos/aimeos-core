<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for all manager created by factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Factory_Interface extends MShop_Common_Manager_Interface
{
	/**
	 * Initializes the manager by using the given context object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context );
}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
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
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context );
}

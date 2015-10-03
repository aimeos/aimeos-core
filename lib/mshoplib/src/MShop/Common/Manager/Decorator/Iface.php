<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Factory interface for managers.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Decorator_Iface
	extends MShop_Common_Manager_Iface
{
	/**
	 * Initializes a new manager decorator object.
	 *
	 * @param MShop_Context_Item_Iface $context Context object with required objects
	 * @param MShop_Common_Manager_Iface $manager Manager object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Iface $context, MShop_Common_Manager_Iface $manager );
}

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
interface MShop_Common_Manager_Decorator_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Initializes a new manager decorator object.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with required objects
	 * @param MShop_Common_Manager_Interface $manager Manager object
	 * @return void
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Common_Manager_Interface $manager );
}

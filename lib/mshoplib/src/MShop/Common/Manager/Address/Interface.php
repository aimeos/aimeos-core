<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Interface for common address DAOs used by the shop.
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Manager_Address_Interface
	extends MShop_Common_Manager_Interface
{
	/**
	 * Initializes a new common address manager object using the given context object.
	 *
	 * @param MShop_Context_Interface $_context Context object with required objects
	 */
	public function __construct( MShop_Context_Item_Interface $context,
		array $config = array(), array $searchConfig = array() );

}

<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
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
	 */
	public function __construct( MShop_Context_Item_Interface $context, MShop_Common_Manager_Interface $manager );
}

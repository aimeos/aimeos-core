<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Interface.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Generic interface for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Factory_Interface
{
	/**
	 *	Creates a manager object.
	 *
	 * @param MShop_Context_Interface $context Context instance with necessary objects
	 * @param string $name Manager name (from configuration or "Default" if null)
	 * @return MShop_Common_Manager_Interface New manager object
	 */
	public static function createManager( MShop_Context_Item_Interface $context, $name = null );
}

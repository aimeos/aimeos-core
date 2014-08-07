<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Generic interface for all HTML client factories.
 *
 * @package Client
 * @subpackage Html
 */
interface Client_Html_Common_Factory_Interface
{
	/**
	 *	Creates a client object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $name Client name (from configuration or "Default" if null)
	 * @return Client_Html_Interface New client object
	 */
	public static function createClient( MShop_Context_Item_Interface $context, array $templatePaths, $name = null );
}
